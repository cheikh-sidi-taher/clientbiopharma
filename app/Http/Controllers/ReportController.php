<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Zone;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$period, $label] = $this->resolvePeriod($request);

        // On charge les agrégats une seule fois pour la vue + export.
        $report = $this->buildReport($period['start'], $period['end']);

        return view('reports.index', [
            'type' => $period['type'],
            'start' => $period['start'],
            'end' => $period['end'],
            'label' => $label,
            'zones' => $report['rows'],
            'totals' => $report['totals'],
        ]);
    }

    public function export(Request $request, string $format)
    {
        [$period, $label] = $this->resolvePeriod($request);
        $report = $this->buildReport($period['start'], $period['end']);

        $filename = $this->makeFilename($period['type'], $period['start'], $period['end'], $format);

        $headers = [
            ['Zone', 'Planifié', 'Réalisé', 'Clients convertis'],
            ['Total', (string) $report['totals']['planned_visits'], (string) $report['totals']['realized_visits'], (string) $report['totals']['clients_created']],
        ];

        if ($format === 'csv') {
            return $this->exportCsv($filename, $report['rows'], $report['totals'], $label);
        }

        if ($format === 'excel') {
            return $this->exportExcelCompatible($filename, $report['rows'], $report['totals'], $label);
        }

        if ($format === 'pdf') {
            return $this->exportPdf($filename, $report['rows'], $report['totals'], $label, $period);
        }

        abort(404);
    }

    private function resolvePeriod(Request $request): array
    {
        $type = $request->get('type', 'journalier');

        $now = now();
        $start = $now->copy();
        $end = $now->copy();

        if ($type === 'journalier') {
            $date = $request->get('date', $now->toDateString());
            $start = Carbon::parse($date)->startOfDay();
            $end = $start->copy()->endOfDay();
        } elseif ($type === 'hebdomadaire') {
            $weekRef = $request->get('week', $now->toDateString());
            $ref = Carbon::parse($weekRef);
            $start = $ref->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
            $end = $ref->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
        } elseif ($type === 'mensuel') {
            $month = $request->get('month', $now->format('Y-m'));
            $start = Carbon::parse($month . '-01')->startOfMonth()->startOfDay();
            $end = $start->copy()->endOfMonth()->endOfDay();
        }

        $period = [
            'type' => $type,
            'start' => $start,
            'end' => $end,
        ];

        $label = match ($type) {
            'journalier' => 'Rapport journalier du ' . $start->locale('fr')->isoFormat('d MMMM YYYY'),
            'hebdomadaire' => 'Rapport hebdomadaire du ' .
                $start->locale('fr')->isoFormat('d MMMM YYYY') . ' au ' .
                $end->locale('fr')->isoFormat('d MMMM YYYY'),
            'mensuel' => 'Rapport mensuel de ' . $start->locale('fr')->isoFormat('MMMM YYYY'),
            default => 'Rapport',
        };

        return [$period, $label];
    }

    private function buildReport(Carbon $start, Carbon $end): array
    {
        $startDate = $start->toDateString();
        $endDate = $end->toDateString();

        // Agrégats visites (par zone)
        $visitsAgg = DB::table('visits')
            ->join('pharmacies', 'visits.pharmacy_id', '=', 'pharmacies.id')
            ->join('zones', 'pharmacies.zone_id', '=', 'zones.id')
            ->select(
                'zones.id as zone_id',
                DB::raw("SUM(CASE WHEN visits.status='planifié' THEN 1 ELSE 0 END) as planned_visits"),
                DB::raw("SUM(CASE WHEN visits.status='réalisé' THEN 1 ELSE 0 END) as realized_visits")
            )
            ->whereBetween('visits.scheduled_date', [$startDate, $endDate])
            ->groupBy('zones.id')
            ->get()
            ->keyBy('zone_id');

        // Agrégats clients convertis (par zone) = clients créés sur la période
        $clientsAgg = DB::table('clients')
            ->join('pharmacies', 'clients.pharmacy_id', '=', 'pharmacies.id')
            ->join('zones', 'pharmacies.zone_id', '=', 'zones.id')
            ->select(
                'zones.id as zone_id',
                DB::raw('COUNT(*) as clients_created')
            )
            ->whereBetween('clients.created_at', [$start, $end])
            ->groupBy('zones.id')
            ->get()
            ->keyBy('zone_id');

        $rows = [];
        $totals = [
            'planned_visits' => 0,
            'realized_visits' => 0,
            'clients_created' => 0,
        ];

        $zones = Zone::where('status', 'active')->orderBy('name')->get();
        foreach ($zones as $zone) {
            $planned = (int) ($visitsAgg[$zone->id]->planned_visits ?? 0);
            $realized = (int) ($visitsAgg[$zone->id]->realized_visits ?? 0);
            $clientsCreated = (int) ($clientsAgg[$zone->id]->clients_created ?? 0);

            $rows[] = [
                'zone' => $zone->name,
                'zone_id' => $zone->id,
                'planned_visits' => $planned,
                'realized_visits' => $realized,
                'clients_created' => $clientsCreated,
            ];

            $totals['planned_visits'] += $planned;
            $totals['realized_visits'] += $realized;
            $totals['clients_created'] += $clientsCreated;
        }

        return [
            'rows' => $rows,
            'totals' => $totals,
        ];
    }

    private function exportCsv(string $filename, array $rows, array $totals, string $label)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
        ];

        $callback = function () use ($rows, $totals, $label) {
            $out = fopen('php://output', 'w');
            // Ligne titre (optionnel)
            fputcsv($out, ['Rapport', $label]);
            fputcsv($out, ['Zone', 'Planifié', 'Réalisé', 'Clients convertis']);

            foreach ($rows as $row) {
                fputcsv($out, [
                    $row['zone'],
                    $row['planned_visits'],
                    $row['realized_visits'],
                    $row['clients_created'],
                ]);
            }

            fputcsv($out, [
                'Total',
                $totals['planned_visits'],
                $totals['realized_visits'],
                $totals['clients_created'],
            ]);

            fclose($out);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    private function exportExcelCompatible(string $filename, array $rows, array $totals, string $label)
    {
        // Excel peut ouvrir un "html table" via extension .xls.
        $tableHtml = View::make('reports.export_excel', [
            'label' => $label,
            'rows' => $rows,
            'totals' => $totals,
        ])->render();

        return response($tableHtml, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function exportPdf(string $filename, array $rows, array $totals, string $label, array $period)
    {
        $pdf = Pdf::loadView('reports.export_pdf', [
            'label' => $label,
            'rows' => $rows,
            'totals' => $totals,
            'type' => $period['type'],
            'start' => $period['start'],
            'end' => $period['end'],
        ]);

        return $pdf->download($filename);
    }

    private function makeFilename(string $type, Carbon $start, Carbon $end, string $format): string
    {
        $startStr = $start->toDateString();
        $endStr = $end->toDateString();

        $safeType = preg_replace('/[^a-z0-9_-]/i', '', $type);

        $ext = $format;
        if ($format === 'excel') {
            $ext = 'xls';
        }

        return sprintf('rapports_%s_%s_au_%s.%s', $safeType, $startStr, $endStr, $ext);
    }
}

