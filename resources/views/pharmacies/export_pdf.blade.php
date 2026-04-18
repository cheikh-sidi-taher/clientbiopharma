<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $label }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #111827; }
        h2 { margin: 0 0 10px 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #f0f4f9; font-weight: 800; }
    </style>
</head>
<body>
    @php
        $coName = \App\Models\Setting::get('company_name', config('app.name'));
        $coAddr = \App\Models\Setting::get('company_address');
        $coPhone = \App\Models\Setting::get('company_phone');
        $coEmail = \App\Models\Setting::get('company_email');
    @endphp
    <div style="margin-bottom:14px;padding-bottom:10px;border-bottom:2px solid #1a6fbf;">
        <div style="font-size:14px;font-weight:800;color:#0d1b2a;">{{ $coName }}</div>
        @if($coAddr)<div style="font-size:9px;color:#4b5563;margin-top:2px;">{{ $coAddr }}</div>@endif
        <div style="font-size:9px;color:#4b5563;margin-top:2px;">
            @if($coPhone)<span>{{ $coPhone }}</span>@endif
            @if($coPhone && $coEmail) &nbsp;|&nbsp; @endif
            @if($coEmail)<span>{{ $coEmail }}</span>@endif
        </div>
    </div>
    <h2>{{ $label }}</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Zone</th>
                <th>Contact</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Partenariat</th>
                <th>Stock</th>
                <th>Livraison</th>
                <th>Formation</th>
                <th>Distribution</th>
            </tr>
        </thead>
        <tbody>
        @foreach($pharmacies as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>
                    <strong>{{ $p->name }}</strong><br>
                    <span>{{ $p->owner_name }}</span><br>
                    <span>{{ $p->address }}</span>
                </td>
                <td>{{ $p->zone?->name }}</td>
                <td>{{ $p->phone }}</td>
                <td>{{ $p->type }}</td>
                <td>{{ $p->interest_status }}</td>
                <td>{{ $p->partnership_type }}</td>
                <td>{{ $p->stock_problem ? 'Oui' : 'Non' }}</td>
                <td>{{ $p->delivery_problem ? 'Oui' : 'Non' }}</td>
                <td>{{ $p->training_need ? 'Oui' : 'Non' }}</td>
                <td>{{ $p->distribution_need ? 'Oui' : 'Non' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>

