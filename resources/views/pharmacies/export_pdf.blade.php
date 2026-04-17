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

