<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ClientExportController extends Controller
{
    public function export(Request $request, string $format)
    {
        $clients = Client::query()->filtered($request)->get();

        $labelParts = ['Export Clients'];
        if ($request->filled('zone_id')) {
            $labelParts[] = 'Zone #'.$request->get('zone_id');
        }
        if ($request->filled('status')) {
            $labelParts[] = 'Statut '.$request->get('status');
        }
        if ($request->filled('commercial_id')) {
            $labelParts[] = 'Commercial #'.$request->get('commercial_id');
        }
        if ($request->filled('search')) {
            $labelParts[] = 'Recherche "'.$request->get('search').'"';
        }
        $label = implode(' — ', $labelParts);

        $filename = $this->makeFilename($format);

        if ($format === 'csv') {
            return $this->exportCsv($filename, $clients, $label);
        }

        if ($format === 'excel') {
            return $this->exportExcelCompatible($filename, $clients, $label);
        }

        if ($format === 'pdf') {
            return $this->exportPdf($filename, $clients, $label);
        }

        abort(404);
    }

    private function exportCsv(string $filename, $clients, string $label)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
        ];

        $callback = function () use ($clients, $label) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Export', $label]);
            fputcsv($out, [
                'ID client',
                'Pharmacie',
                'Zone',
                'Responsable',
                'Téléphone pharmacie',
                'Commercial',
                'E-mail commercial',
                'Statut client',
                'Limite crédit',
                'Conditions paiement',
                'Créé le',
            ]);

            foreach ($clients as $c) {
                $p = $c->pharmacy;
                fputcsv($out, [
                    $c->id,
                    $p?->name,
                    $p?->zone?->name,
                    $p?->owner_name,
                    $p?->phone,
                    $c->commercial?->name,
                    $c->commercial?->email,
                    $c->status,
                    $c->credit_limit,
                    $c->payment_terms,
                    optional($c->created_at)->toDateTimeString(),
                ]);
            }

            fclose($out);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    private function exportExcelCompatible(string $filename, $clients, string $label)
    {
        $html = View::make('clients.export_excel', [
            'label' => $label,
            'clients' => $clients,
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function exportPdf(string $filename, $clients, string $label)
    {
        $pdf = Pdf::loadView('clients.export_pdf', [
            'label' => $label,
            'clients' => $clients,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    private function makeFilename(string $format): string
    {
        $base = 'clients_'.now()->format('Y-m-d_His');
        $ext = $format === 'excel' ? 'xls' : $format;

        return $base.'.'.$ext;
    }
}
