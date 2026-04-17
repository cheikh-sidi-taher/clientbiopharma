<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $label }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111827; }
        h2 { margin: 0 0 12px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f0f4f9; font-weight: 800; }
        .total { font-weight: 900; background: #f0f4f9; }
    </style>
</head>
<body>
    <h2>{{ $label }}</h2>
    <p style="margin:0 0 14px 0;color:#6b7280;">
        Période : {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Zone</th>
                <th>Planifié</th>
                <th>Réalisé</th>
                <th>Clients convertis</th>
            </tr>
        </thead>
        <tbody>
        @foreach($rows as $row)
            <tr>
                <td>{{ $row['zone'] }}</td>
                <td>{{ $row['planned_visits'] }}</td>
                <td>{{ $row['realized_visits'] }}</td>
                <td>{{ $row['clients_created'] }}</td>
            </tr>
        @endforeach
            <tr class="total">
                <td>Total</td>
                <td>{{ $totals['planned_visits'] }}</td>
                <td>{{ $totals['realized_visits'] }}</td>
                <td>{{ $totals['clients_created'] }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>

