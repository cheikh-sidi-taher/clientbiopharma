<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $label }}</title>
</head>
<body>
    <h2 style="font-family:Arial, sans-serif;margin:0 0 12px 0;">{{ $label }}</h2>

    <table cellpadding="6" cellspacing="0" border="1" style="border-collapse:collapse;font-family:Arial, sans-serif;width:100%;">
        <thead>
            <tr style="background:#f0f4f9;">
                <th>ID</th>
                <th>Pharmacie</th>
                <th>Zone</th>
                <th>Responsable</th>
                <th>Tél.</th>
                <th>Commercial</th>
                <th>Statut</th>
                <th>Crédit</th>
                <th>Conditions</th>
                <th>Créé le</th>
            </tr>
        </thead>
        <tbody>
        @foreach($clients as $c)
            @php $p = $c->pharmacy; @endphp
            <tr>
                <td>{{ $c->id }}</td>
                <td>{{ $p?->name }}</td>
                <td>{{ $p?->zone?->name }}</td>
                <td>{{ $p?->owner_name }}</td>
                <td>{{ $p?->phone }}</td>
                <td>{{ $c->commercial?->name }}</td>
                <td>{{ $c->status }}</td>
                <td>{{ number_format((float) $c->credit_limit, 2, ',', ' ') }}</td>
                <td>{{ $c->payment_terms }}</td>
                <td>{{ optional($c->created_at)->toDateTimeString() }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
