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
                <th style="text-align:left;">Zone</th>
                <th style="text-align:left;">Planifié</th>
                <th style="text-align:left;">Réalisé</th>
                <th style="text-align:left;">Clients convertis</th>
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
            <tr style="font-weight:bold;background:#f0f4f9;">
                <td>Total</td>
                <td>{{ $totals['planned_visits'] }}</td>
                <td>{{ $totals['realized_visits'] }}</td>
                <td>{{ $totals['clients_created'] }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>

