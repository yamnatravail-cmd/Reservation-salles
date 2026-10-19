<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture Achat #{{ $facture->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            margin: 30px;
        }
        .header {
            text-align: center;
            font-size: 22px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .section-title {
            background-color: #f0f0f0;
            padding: 8px;
            font-weight: bold;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        table th, table td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }
        .total {
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
        }
        p {
            margin: 5px 0;
        }
    </style>
</head>
<body>

    <div class="header">Facture d'Achat #{{ $facture->id }}</div>

    <!-- Fournisseur -->
    <div class="section-title">Informations du Fournisseur</div>
    <p><strong>Nom :</strong> {{ $facture->purchase->fournisseur->nom ?? 'N/A' }}</p>
    <p><strong>Email :</strong> {{ $facture->purchase->fournisseur->email ?? 'N/A' }}</p>
    <p><strong>Téléphone :</strong> {{ $facture->purchase->fournisseur->telephone ?? 'N/A' }}</p>
    <p><strong>Mobile :</strong> {{ $facture->purchase->fournisseur->mobile ?? 'N/A' }}</p>

    <!-- Produits -->
    <div class="section-title">Produits Achetés</div>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Type</th>
                <th>Quantité</th>
                <th>Prix Unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facture->purchase->produits as $produit)
                @php
                    $prixUnitaire = $produit->pivot->prix_unitaire ?? $produit->prix ?? 0;
                    $totalLigne = $produit->pivot->quantite * $prixUnitaire;
                @endphp
                <tr>
                    <td>{{ $produit->nom }}</td>
                    <td>{{ ucfirst($produit->type) }}</td>
                    <td>{{ $produit->pivot->quantite }}</td>
                    <td>{{ number_format($prixUnitaire, 2, ',', ' ') }} DH</td>
                    <td>{{ number_format($totalLigne, 2, ',', ' ') }} DH</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Résumé -->
    <div class="section-title">Résumé de la Facture</div>
    <p><strong>Date d'Émission :</strong> {{ $facture->date_emission }}</p>
    <p><strong>Statut :</strong> {{ $facture->statut }}</p>
    <p><strong>Montant Total (avec TVA) :</strong> {{ number_format($facture->montant_total, 2, ',', ' ') }} DH</p>
    <p><strong>Montant Payé :</strong> {{ number_format($facture->amount_paid, 2, ',', ' ') }} DH</p>
    <p><strong>Montant Restant :</strong> {{ number_format($facture->amount_remaining, 2, ',', ' ') }} DH</p>
    @if($facture->tva)
        <p><strong>TVA :</strong> {{ $facture->tva->pourcentage }}%</p>
    @endif
<!-- Paiements -->
@if($facture->paiements && $facture->paiements->count() > 0)
    <div class="section-title">Paiements Effectués</div>
    <table>
        <thead>
            <tr>
                <th>Date de Paiement</th>
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facture->paiements as $paiement)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</td>
                    <td>{{ number_format($paiement->montant, 2, ',', ' ') }} DH</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

</body>
</html>
