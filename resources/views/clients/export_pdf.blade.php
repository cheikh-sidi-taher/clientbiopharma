<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $label }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9px; color: #111827; }
        h2 { margin: 0 0 10px 0; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 5px; text-align: left; vertical-align: top; }
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
    <div style="margin-bottom:12px;padding-bottom:8px;border-bottom:2px solid #1a6fbf;">
        <div style="font-size:13px;font-weight:800;color:#0d1b2a;">{{ $coName }}</div>
        @if($coAddr)<div style="font-size:8px;color:#4b5563;margin-top:2px;">{{ $coAddr }}</div>@endif
        <div style="font-size:8px;color:#4b5563;margin-top:2px;">
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
                <th>Pharmacie / Zone</th>
                <th>Contact</th>
                <th>Commercial</th>
                <th>Statut</th>
                <th>Crédit</th>
                <th>Conditions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($clients as $c)
            @php $p = $c->pharmacy; @endphp
            <tr>
                <td>{{ $c->id }}</td>
                <td>
                    <strong>{{ $p?->name }}</strong><br>
                    <span style="color:#64748b;">{{ $p?->zone?->name }}</span>
                </td>
                <td>
                    {{ $p?->owner_name }}<br>
                    {{ $p?->phone }}<br>
                    <span style="font-size:8px;">{{ $p?->address }}</span>
                </td>
                <td>{{ $c->commercial?->name }}<br><span style="font-size:8px;">{{ $c->commercial?->email }}</span></td>
                <td>{{ $c->status }}</td>
                <td>{{ number_format((float) $c->credit_limit, 2, ',', ' ') }}</td>
                <td>{{ Str::limit($c->payment_terms ?? '', 120) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
