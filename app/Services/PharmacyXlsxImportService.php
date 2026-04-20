<?php

namespace App\Services;

use App\Models\Pharmacy;
use App\Models\Zone;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class PharmacyXlsxImportService
{
    /**
     * @return array{created:int,updated:int,skipped:int,zones_created:int,errors:array<int,string>}
     */
    public function import(UploadedFile $file): array
    {
        $rows = $this->extractRows($file->getRealPath());
        if ($rows === []) {
            return ['created' => 0, 'updated' => 0, 'skipped' => 0, 'zones_created' => 0, 'errors' => ['Le fichier Excel est vide.']];
        }

        $header = array_shift($rows);
        $columnMap = $this->buildColumnMap($header ?? []);

        $requiredColumns = ['zone', 'name'];
        foreach ($requiredColumns as $column) {
            if (! isset($columnMap[$column])) {
                throw new RuntimeException('Colonnes obligatoires manquantes : zone et nom pharmacie.');
            }
        }

        $zonesById = Zone::query()->pluck('id', 'id')->all();
        $zones = Zone::query()->get(['id', 'name']);
        $zonesByName = [];
        foreach ($zones as $zone) {
            $zonesByName[$this->normalizeZoneName($zone->name)] = $zone->id;
        }

        $stats = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'zones_created' => 0, 'errors' => []];
        $rowNumber = 1;

        foreach ($rows as $row) {
            $rowNumber++;

            $name = $this->cell($row, $columnMap, 'name');
            $zoneRaw = $this->cell($row, $columnMap, 'zone');

            if ($name === '' && $zoneRaw === '') {
                $stats['skipped']++;
                continue;
            }

            $zoneId = $this->resolveZoneId($zoneRaw, $zonesById, $zonesByName);
            if ($zoneId === null) {
                $zoneName = $this->nullable($zoneRaw);
                if ($zoneName === null) {
                    $stats['errors'][] = "Ligne {$rowNumber}: zone manquante.";
                    continue;
                }

                $zone = Zone::create([
                    'name' => $zoneName,
                    'status' => 'active',
                ]);

                $zoneId = $zone->id;
                $zonesById[$zoneId] = $zoneId;
                $zonesByName[$this->normalizeZoneName($zoneName)] = $zoneId;
                $stats['zones_created']++;
            }

            if ($name === '') {
                $stats['errors'][] = "Ligne {$rowNumber}: nom de pharmacie manquant.";
                continue;
            }

            $payload = [
                'zone_id' => $zoneId,
                'name' => $name,
                'owner_name' => $this->nullable($this->cell($row, $columnMap, 'owner_name')),
                'phone' => $this->nullable($this->cell($row, $columnMap, 'phone')),
                'address' => $this->nullable($this->cell($row, $columnMap, 'address')),
                'type' => $this->enumOrDefault($this->cell($row, $columnMap, 'type'), ['publique', 'privée', 'clinique'], 'privée'),
                'best_selling_products' => $this->nullable($this->cell($row, $columnMap, 'best_selling_products')),
                'stock_problem' => $this->toBool($this->cell($row, $columnMap, 'stock_problem')),
                'delivery_problem' => $this->toBool($this->cell($row, $columnMap, 'delivery_problem')),
                'training_need' => $this->toBool($this->cell($row, $columnMap, 'training_need')),
                'distribution_need' => $this->toBool($this->cell($row, $columnMap, 'distribution_need')),
                'interest_status' => $this->enumOrDefault(
                    $this->cell($row, $columnMap, 'interest_status'),
                    ['non_visité', 'visité', 'intéressé', 'non_intéressé', 'client'],
                    'non_visité'
                ),
                'partnership_type' => $this->enumOrDefault(
                    $this->cell($row, $columnMap, 'partnership_type'),
                    ['aucun', 'distributeur', 'partenaire', 'client_direct'],
                    'aucun'
                ),
                'notes' => $this->nullable($this->cell($row, $columnMap, 'notes')),
                'latitude' => $this->toFloatOrNull($this->cell($row, $columnMap, 'latitude')),
                'longitude' => $this->toFloatOrNull($this->cell($row, $columnMap, 'longitude')),
            ];

            $existing = Pharmacy::query()
                ->where('name', $payload['name'])
                ->when($payload['phone'], fn ($query) => $query->where('phone', $payload['phone']))
                ->first();

            if ($existing) {
                $existing->update($payload);
                $stats['updated']++;
                continue;
            }

            $payload['created_by'] = Auth::id();
            Pharmacy::create($payload);
            $stats['created']++;
        }

        return $stats;
    }

    /**
     * @return array<int,array<int,string>>
     */
    private function extractRows(string $path): array
    {
        if (class_exists(ZipArchive::class)) {
            $zip = new ZipArchive;
            if ($zip->open($path) !== true) {
                throw new RuntimeException('Impossible d\'ouvrir le fichier Excel.');
            }

            $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml') ?: '';
            $worksheetXml = $zip->getFromName('xl/worksheets/sheet1.xml') ?: '';
            $zip->close();

            if ($worksheetXml === '') {
                throw new RuntimeException('Aucune feuille "sheet1" trouvée dans ce fichier Excel.');
            }

            $sharedStrings = $this->parseSharedStrings($sharedStringsXml);
            return $this->parseWorksheetRows($worksheetXml, $sharedStrings);
        }

        return $this->extractRowsWithSystemUnzip($path);
    }

    /**
     * @return array<int,string>
     */
    private function parseSharedStrings(string $xml): array
    {
        if ($xml === '') {
            return [];
        }

        $doc = simplexml_load_string($xml);
        if (! $doc instanceof SimpleXMLElement) {
            return [];
        }

        $doc->registerXPathNamespace('s', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $items = $doc->xpath('//s:si') ?: [];

        return array_map(function (SimpleXMLElement $item): string {
            $item->registerXPathNamespace('s', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $fragments = $item->xpath('.//s:t') ?: [];

            return trim(implode('', array_map('strval', $fragments)));
        }, $items);
    }

    /**
     * @param array<int,string> $sharedStrings
     * @return array<int,array<int,string>>
     */
    private function parseWorksheetRows(string $xml, array $sharedStrings): array
    {
        $doc = simplexml_load_string($xml);
        if (! $doc instanceof SimpleXMLElement) {
            return [];
        }

        $doc->registerXPathNamespace('s', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $rows = $doc->xpath('//*[local-name()="sheetData"]/*[local-name()="row"]') ?: [];
        $dataset = [];

        foreach ($rows as $row) {
            $line = [];
            $cells = $row->xpath('./*[local-name()="c"]') ?: [];
            foreach ($cells as $cell) {
                $ref = (string) $cell['r'];
                $colIndex = $this->columnIndexFromCellRef($ref);
                $value = '';
                $type = (string) $cell['t'];

                if ($type === 'inlineStr') {
                    $inlineText = $cell->xpath('./*[local-name()="is"]/*[local-name()="t"]') ?: [];
                    $value = isset($inlineText[0]) ? trim((string) $inlineText[0]) : '';
                } else {
                    $rawNode = $cell->xpath('./*[local-name()="v"]') ?: [];
                    $raw = trim(isset($rawNode[0]) ? (string) $rawNode[0] : '');
                    if ($type === 's') {
                        $value = $sharedStrings[(int) $raw] ?? '';
                    } else {
                        $value = $raw;
                    }
                }

                $line[$colIndex] = $value;
            }

            if ($line === []) {
                continue;
            }

            ksort($line);
            $maxIndex = (int) array_key_last($line);
            $normalized = [];

            for ($i = 1; $i <= $maxIndex; $i++) {
                $normalized[] = $line[$i] ?? '';
            }

            $dataset[] = $normalized;
        }

        return $dataset;
    }

    private function columnIndexFromCellRef(string $cellRef): int
    {
        $letters = preg_replace('/[^A-Z]/', '', strtoupper($cellRef));
        $index = 0;

        for ($i = 0; $i < strlen($letters); $i++) {
            $index = ($index * 26) + (ord($letters[$i]) - 64);
        }

        return max($index, 1);
    }

    /**
     * @param array<int,string> $header
     * @return array<string,int>
     */
    private function buildColumnMap(array $header): array
    {
        $map = [];
        $aliases = [
            'zone' => ['zone', 'zone_id'],
            'name' => ['name', 'nom', 'nom_pharmacie', 'pharmacie'],
            'owner_name' => ['owner_name', 'responsable', 'proprietaire'],
            'phone' => ['phone', 'telephone', 'tel'],
            'address' => ['address', 'adresse'],
            'type' => ['type'],
            'best_selling_products' => ['best_selling_products', 'produits_plus_vendus', 'produits'],
            'stock_problem' => ['stock_problem', 'probleme_stock'],
            'delivery_problem' => ['delivery_problem', 'probleme_livraison'],
            'training_need' => ['training_need', 'besoin_formation'],
            'distribution_need' => ['distribution_need', 'besoin_distribution'],
            'interest_status' => ['interest_status', 'statut_interet'],
            'partnership_type' => ['partnership_type', 'type_partenariat'],
            'notes' => ['notes', 'remarques'],
            'latitude' => ['latitude', 'lat'],
            'longitude' => ['longitude', 'lng', 'long'],
        ];

        foreach ($header as $index => $label) {
            $normalized = $this->normalizeHeader($label);
            foreach ($aliases as $key => $options) {
                if (in_array($normalized, $options, true)) {
                    $map[$key] = $index;
                    break;
                }
            }
        }

        return $map;
    }

    private function normalizeHeader(string $header): string
    {
        $header = trim(mb_strtolower($header));
        $search = [' ', '-', '.', 'é', 'è', 'ê', 'à', 'â', 'î', 'ï', 'ô', 'ù', 'û', 'ç', '\''];
        $replace = ['_', '_', '_', 'e', 'e', 'e', 'a', 'a', 'i', 'i', 'o', 'u', 'u', 'c', ''];

        return str_replace($search, $replace, $header);
    }

    /**
     * @param array<int,string> $row
     * @param array<string,int> $columnMap
     */
    private function cell(array $row, array $columnMap, string $column): string
    {
        if (! isset($columnMap[$column])) {
            return '';
        }

        return trim((string) ($row[$columnMap[$column]] ?? ''));
    }

    /**
     * @param array<int,int> $zonesById
     * @param array<string,int> $zonesByName
     */
    private function resolveZoneId(string $rawZone, array $zonesById, array $zonesByName): ?int
    {
        $rawZone = trim($rawZone);
        if ($rawZone === '') {
            return null;
        }

        if (ctype_digit($rawZone)) {
            $id = (int) $rawZone;
            return $zonesById[$id] ?? null;
        }

        return $zonesByName[$this->normalizeZoneName($rawZone)] ?? null;
    }

    private function normalizeZoneName(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = str_replace(['-', '.', ',', ';'], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;
        $value = Str::ascii($value);

        return trim($value);
    }

    private function toBool(string $value): bool
    {
        return in_array(mb_strtolower(trim($value)), ['1', 'true', 'oui', 'yes', 'vrai'], true);
    }

    private function nullable(string $value): ?string
    {
        $value = trim($value);

        return $value === '' ? null : $value;
    }

    /**
     * @param array<int,string> $allowed
     */
    private function enumOrDefault(string $value, array $allowed, string $default): string
    {
        return in_array($value, $allowed, true) ? $value : $default;
    }

    private function toFloatOrNull(string $value): ?float
    {
        if ($value === '') {
            return null;
        }

        $normalized = str_replace(',', '.', $value);

        return is_numeric($normalized) ? (float) $normalized : null;
    }

    /**
     * Fallback pour les environnements Windows sans extension php_zip.
     *
     * @return array<int,array<int,string>>
     */
    private function extractRowsWithSystemUnzip(string $path): array
    {
        if (stripos(PHP_OS_FAMILY, 'Windows') === false) {
            throw new RuntimeException('ZipArchive n\'est pas disponible. Activez l\'extension PHP "zip" pour importer des fichiers .xlsx.');
        }

        if (! function_exists('exec')) {
            throw new RuntimeException('ZipArchive n\'est pas disponible et la fonction exec() est désactivée.');
        }

        $tempRoot = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'xlsx-import-'.Str::uuid();
        if (! @mkdir($tempRoot, 0777, true) && ! is_dir($tempRoot)) {
            throw new RuntimeException('Impossible de préparer un répertoire temporaire pour l\'import.');
        }

        $sourceZipPath = $tempRoot.DIRECTORY_SEPARATOR.'source.xlsx.zip';
        $extractPath = $tempRoot.DIRECTORY_SEPARATOR.'unzipped';
        @mkdir($extractPath, 0777, true);

        if (! @copy($path, $sourceZipPath)) {
            $this->cleanupDirectory($tempRoot);
            throw new RuntimeException('Impossible de préparer le fichier Excel pour extraction.');
        }

        $scriptPath = $tempRoot.DIRECTORY_SEPARATOR.'extract.ps1';
        $script = <<<'PS1'
param(
    [Parameter(Mandatory=$true)][string]$Source,
    [Parameter(Mandatory=$true)][string]$Destination
)
Expand-Archive -LiteralPath $Source -DestinationPath $Destination -Force
PS1;
        @file_put_contents($scriptPath, $script);

        $errors = [];

        $powerShellCommand =
            'powershell -NoProfile -NonInteractive -ExecutionPolicy Bypass -File '
            .escapeshellarg($scriptPath).' '
            .escapeshellarg($sourceZipPath).' '
            .escapeshellarg($extractPath).' 2>&1';

        $output = [];
        $exitCode = 0;
        @exec($powerShellCommand, $output, $exitCode);

        if ($exitCode !== 0) {
            $errors[] = 'PowerShell: '.trim(implode(' | ', $output));

            $tarCommand = 'tar -xf '.escapeshellarg($sourceZipPath).' -C '.escapeshellarg($extractPath).' 2>&1';
            $tarOutput = [];
            $tarExitCode = 0;
            @exec($tarCommand, $tarOutput, $tarExitCode);

            if ($tarExitCode !== 0) {
                $errors[] = 'tar: '.trim(implode(' | ', $tarOutput));
                $this->cleanupDirectory($tempRoot);
                throw new RuntimeException('Impossible de décompresser le fichier Excel sans l\'extension PHP zip. Détails: '.implode(' || ', array_filter($errors)));
            }
        }

        $worksheetPath = $extractPath.DIRECTORY_SEPARATOR.'xl'.DIRECTORY_SEPARATOR.'worksheets'.DIRECTORY_SEPARATOR.'sheet1.xml';
        $sharedStringsPath = $extractPath.DIRECTORY_SEPARATOR.'xl'.DIRECTORY_SEPARATOR.'sharedStrings.xml';

        if (! is_file($worksheetPath)) {
            $this->cleanupDirectory($tempRoot);
            throw new RuntimeException('Aucune feuille "sheet1" trouvée dans ce fichier Excel.');
        }

        $worksheetXml = file_get_contents($worksheetPath) ?: '';
        $sharedStringsXml = is_file($sharedStringsPath) ? (file_get_contents($sharedStringsPath) ?: '') : '';

        $this->cleanupDirectory($tempRoot);

        $sharedStrings = $this->parseSharedStrings($sharedStringsXml);
        return $this->parseWorksheetRows($worksheetXml, $sharedStrings);
    }

    private function cleanupDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        $items = array_diff(scandir($directory) ?: [], ['.', '..']);
        foreach ($items as $item) {
            $path = $directory.DIRECTORY_SEPARATOR.$item;
            if (is_dir($path)) {
                $this->cleanupDirectory($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($directory);
    }
}
