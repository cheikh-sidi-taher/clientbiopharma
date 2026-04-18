<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class PharmacyExportController extends Controller
{
    public function export(Request $request, string $format)
    {
        $query = Pharmacy::with(['zone', 'creator'])->latest();

        // Même logique que l'index (filtres)
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('owner_name', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%");
            });
        }

        if ($zoneId = $request->get('zone_id')) {
            $query->where('zone_id', $zoneId);
        }

        if ($interest = $request->get('interest_status')) {
            $query->where('interest_status', $interest);
        }

        $pharmacies = $query->get();

        $labelParts = ['Export Pharmacies'];
        if ($request->filled('zone_id')) {
            $labelParts[] = 'Zone #'.$request->get('zone_id');
        }
        if ($request->filled('interest_status')) {
            $labelParts[] = 'Statut '.$request->get('interest_status');
        }
        if ($request->filled('search')) {
            $labelParts[] = 'Recherche "'.$request->get('search').'"';
        }
        $label = implode(' — ', $labelParts);

        $filename = $this->makeFilename($format);

        if ($format === 'csv') {
            return $this->exportCsv($filename, $pharmacies, $label);
        }

        if ($format === 'excel') {
            return $this->exportExcelCompatible($filename, $pharmacies, $label);
        }

        if ($format === 'pdf') {
            return $this->exportPdf($filename, $pharmacies, $label);
        }

        abort(404);
    }

    private function exportCsv(string $filename, $pharmacies, string $label)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
        ];

        $callback = function () use ($pharmacies, $label) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Export', $label]);
            fputcsv($out, [
                'ID', 'Nom', 'Responsable', 'Téléphone', 'Adresse', 'Zone',
                'Type', 'Statut intérêt', 'Partenariat', 'Problème stock',
                'Problème livraison', 'Besoin formation', 'Besoin distribution',
                'Créé le',
            ]);

            foreach ($pharmacies as $p) {
                fputcsv($out, [
                    $p->id,
                    $p->name,
                    $p->owner_name,
                    $p->phone,
                    $p->address,
                    $p->zone?->name,
                    $p->type,
                    $p->interest_status,
                    $p->partnership_type,
                    $p->stock_problem ? '1' : '0',
                    $p->delivery_problem ? '1' : '0',
                    $p->training_need ? '1' : '0',
                    $p->distribution_need ? '1' : '0',
                    optional($p->created_at)->toDateTimeString(),
                ]);
            }

            fclose($out);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    private function exportExcelCompatible(string $filename, $pharmacies, string $label)
    {
        $html = View::make('pharmacies.export_excel', [
            'label' => $label,
            'pharmacies' => $pharmacies,
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function exportPdf(string $filename, $pharmacies, string $label)
    {
        $pdf = Pdf::loadView('pharmacies.export_pdf', [
            'label' => $label,
            'pharmacies' => $pharmacies,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    private function makeFilename(string $format): string
    {
        $base = 'pharmacies_'.now()->format('Y-m-d_His');
        $ext = $format === 'excel' ? 'xls' : $format;

        return $base.'.'.$ext;
    }
}
