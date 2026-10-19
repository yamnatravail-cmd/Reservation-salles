<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Facture #{{ $facture->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        h1 { text-align: center; }
    </style>
</head>
<body>
    <h1>Facture #{{ $facture->id }}</h1>
    <p><strong>Client:</strong> {{ $facture->demande->client->nom ?? 'Nom non disponible' }} {{ $facture->demande->client->Prenom ?? 'Prenom non disponible' }}</p>
    <p><strong>Date d'émission:</strong> {{ $facture->date_emission }}</p>
    <p><strong>Status:</strong> {{ $facture->status }}</p>
    <p><strong>Salle:</strong> {{ $facture->demande->salle->nom }}</p>
    <p><strong>Montant HT:</strong> {{ number_format($facture->montant_total - ($facture->montant_total * $facture->tva->pourcentage / (100 + $facture->tva->pourcentage)), 2) }} €</p>
    <p><strong>TVA ({{ $facture->tva->pourcentage }}%):</strong> {{ number_format(($facture->montant_total * $facture->tva->pourcentage) / (100 + $facture->tva->pourcentage), 2) }} €</p>
    <p><strong>Montant TTC:</strong> {{ number_format($facture->montant_total, 2) }} €</p>

    <h2>Services choisis :</h2>

    @if(!empty($facture->demande->services_details) && $facture->demande->services_details->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Nom du service</th>
                    <th>Prix</th>
                </tr>
            </thead>
            <tbody>
                @foreach($facture->demande->services_details as $service)
                    <tr>
                        <td>{{ $service->produit->nom ?? 'Nom non disponible' }}</td>
                        <td>{{ number_format($service->produit->prix ?? 0, 2) }} €</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Aucun service sélectionné.</p>
    @endif

    <h2>Paiements :</h2>

    @if(!empty($facture->paiements) && $facture->paiements->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Date de paiement</th>
                    <th>Montant</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($facture->paiements as $paiement)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</td>
                        <td>{{ number_format($paiement->montant, 2) }} €</td>
                        <td>{{ ucfirst($paiement->statut) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Aucun paiement enregistré.</p>
    @endif

    <p><strong>Montant payé:</strong> {{ number_format($facture->montant_paye, 2) }} €</p>
    <p><strong>Reste à payer:</strong> {{ number_format($facture->reste_a_payer, 2) }} €</p>

</body>
</html>
