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
                <th>Nom</th>
                <th>Responsable</th>
                <th>Téléphone</th>
                <th>Adresse</th>
                <th>Zone</th>
                <th>Type</th>
                <th>Statut intérêt</th>
                <th>Partenariat</th>
                <th>Stock</th>
                <th>Livraison</th>
                <th>Formation</th>
                <th>Distribution</th>
                <th>Créé le</th>
            </tr>
        </thead>
        <tbody>
        @foreach($pharmacies as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->owner_name }}</td>
                <td>{{ $p->phone }}</td>
                <td>{{ $p->address }}</td>
                <td>{{ $p->zone?->name }}</td>
                <td>{{ $p->type }}</td>
                <td>{{ $p->interest_status }}</td>
                <td>{{ $p->partnership_type }}</td>
                <td>{{ $p->stock_problem ? '1' : '0' }}</td>
                <td>{{ $p->delivery_problem ? '1' : '0' }}</td>
                <td>{{ $p->training_need ? '1' : '0' }}</td>
                <td>{{ $p->distribution_need ? '1' : '0' }}</td>
                <td>{{ optional($p->created_at)->toDateTimeString() }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>

