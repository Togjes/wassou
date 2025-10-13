<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>État des Lieux - {{ ucfirst($etatLieux->type_etat) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #333;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2c3e50;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 22pt;
            margin-bottom: 8px;
        }

        .header .type {
            color: {{ $etatLieux->type_etat === 'entree' ? '#27ae60' : '#e74c3c' }};
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header .date {
            margin-top: 8px;
            font-size: 11pt;
            color: #555;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            background-color: #2c3e50;
            color: white;
            padding: 8px 12px;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .info-box {
            background-color: #ecf0f1;
            padding: 12px;
            margin-bottom: 12px;
            border-left: 4px solid #3498db;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 6px;
        }

        .info-label {
            display: table-cell;
            width: 35%;
            font-weight: bold;
            color: #555;
        }

        .info-value {
            display: table-cell;
            width: 65%;
            color: #222;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th {
            background-color: #34495e;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10pt;
            border: 1px solid #2c3e50;
        }

        table td {
            padding: 8px;
            border: 1px solid #bdc3c7;
            font-size: 9pt;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .etat-bon {
            color: #27ae60;
            font-weight: bold;
        }

        .etat-moyen {
            color: #f39c12;
            font-weight: bold;
        }

        .etat-mauvais {
            color: #e74c3c;
            font-weight: bold;
        }

        .etat-absent {
            color: #95a5a6;
            font-weight: bold;
        }

        .degats-box {
            background-color: #fee;
            border-left: 4px solid #e74c3c;
            padding: 12px;
            margin-top: 10px;
        }

        .total-reparations {
            background-color: #e74c3c;
            color: white;
            padding: 10px;
            font-size: 13pt;
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
        }

        .observations-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px;
            margin-top: 10px;
            font-style: italic;
        }

        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-box {
            display: inline-block;
            width: 48%;
            vertical-align: top;
            padding: 12px;
            border: 2px solid #2c3e50;
            min-height: 100px;
        }

        .signature-box:first-child {
            margin-right: 3%;
        }

        .signature-label {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 11pt;
        }

        .signature-name {
            font-weight: bold;
            margin-top: 40px;
            border-top: 1px solid #000;
            padding-top: 5px;
            text-align: center;
        }

        .signature-date {
            text-align: center;
            font-size: 9pt;
            color: #27ae60;
            margin-top: 5px;
        }

        .footer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 2px solid #2c3e50;
            text-align: center;
            font-size: 8pt;
            color: #777;
        }

        .contrat-info {
            background-color: #d5e8f7;
            border-left: 4px solid #2980b9;
            padding: 12px;
            margin-bottom: 15px;
        }

        .bien-info {
            background-color: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 12px;
            margin-bottom: 15px;
        }

        @page {
            margin: 15mm;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>ÉTAT DES LIEUX</h1>
        <div class="type">{{ $etatLieux->type_etat === 'entree' ? 'D\'ENTRÉE' : 'DE SORTIE' }}</div>
        <div class="date">
            Effectué le {{ $etatLieux->date_etat->format('d/m/Y') }}
        </div>
    </div>

    <!-- Informations du Contrat -->
    <div class="section">
        <div class="section-title">INFORMATIONS DU CONTRAT</div>
        <div class="contrat-info">
            <div class="info-row">
                <div class="info-label">Numéro de contrat :</div>
                <div class="info-value"><strong>{{ $contrat->numero_contrat }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Date d'établissement :</div>
                <div class="info-value">{{ $contrat->date_etablissement->format('d/m/Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Statut du contrat :</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $contrat->statut)) }}</div>
            </div>
        </div>
    </div>

    <!-- Parties -->
    <div class="section">
        <div class="section-title">LES PARTIES</div>
        
        <div class="info-box">
            <strong style="color: #2c3e50; font-size: 11pt;">Propriétaire (Bailleur)</strong>
            <div class="info-row" style="margin-top: 8px;">
                <div class="info-label">Nom :</div>
                <div class="info-value">{{ $contrat->proprietaire->user->full_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Téléphone :</div>
                <div class="info-value">{{ $contrat->proprietaire->user->phone }}</div>
            </div>
        </div>

        <div class="info-box">
            <strong style="color: #2c3e50; font-size: 11pt;">Locataire (Preneur)</strong>
            <div class="info-row" style="margin-top: 8px;">
                <div class="info-label">Nom :</div>
                <div class="info-value">{{ $contrat->locataire->user->full_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Téléphone :</div>
                <div class="info-value">{{ $contrat->locataire->user->phone }}</div>
            </div>
        </div>
    </div>

    <!-- Bien Concerné -->
    <div class="section">
        <div class="section-title">BIEN CONCERNÉ</div>
        <div class="bien-info">
            <div class="info-row">
                <div class="info-label">Bien immobilier :</div>
                <div class="info-value"><strong>{{ $contrat->chambre->bien->titre }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Localisation :</div>
                <div class="info-value">{{ $contrat->chambre->bien->quartier }}, {{ $contrat->chambre->bien->ville }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Chambre :</div>
                <div class="info-value"><strong>{{ $contrat->chambre->nom_chambre }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Type :</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $contrat->chambre->type_chambre)) }}</div>
            </div>
            @if($contrat->chambre->surface_m2)
            <div class="info-row">
                <div class="info-label">Surface :</div>
                <div class="info-value">{{ $contrat->chambre->surface_m2 }} m²</div>
            </div>
            @endif
        </div>
    </div>

    <!-- État des Équipements -->
    <div class="section">
        <div class="section-title">ÉTAT DES ÉQUIPEMENTS</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 30%;">Équipement</th>
                    <th style="width: 20%;">État</th>
                    <th style="width: 50%;">Observations</th>
                </tr>
            </thead>
            <tbody>
                @foreach($etatLieux->details_equipements as $nom => $details)
                    <tr>
                        <td><strong>{{ $nom }}</strong></td>
                        <td>
                            @if($details['etat'] === 'bon')
                                <span class="etat-bon">✓ Bon état</span>
                            @elseif($details['etat'] === 'moyen')
                                <span class="etat-moyen">~ État moyen</span>
                            @elseif($details['etat'] === 'mauvais')
                                <span class="etat-mauvais">✗ Mauvais état</span>
                            @else
                                <span class="etat-absent">- Absent</span>
                            @endif
                        </td>
                        <td>{{ $details['observations'] ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Observations Générales -->
    @if($etatLieux->observations)
    <div class="section">
        <div class="section-title">OBSERVATIONS GÉNÉRALES</div>
        <div class="observations-box">
            {{ $etatLieux->observations }}
        </div>
    </div>
    @endif

    <!-- Dégâts Constatés (uniquement pour sortie) -->
    @if($etatLieux->type_etat === 'sortie' && $etatLieux->degats_constates && count($etatLieux->degats_constates) > 0)
    <div class="section">
        <div class="section-title">DÉGÂTS CONSTATÉS</div>
        <div class="degats-box">
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">N°</th>
                        <th style="width: 60%;">Description du Dégât</th>
                        <th style="width: 30%;">Coût de Réparation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($etatLieux->degats_constates as $index => $degat)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td>{{ $degat['description'] }}</td>
                            <td style="text-align: right; font-weight: bold;">
                                {{ number_format($degat['cout'], 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="total-reparations">
            TOTAL DES RÉPARATIONS : {{ number_format($etatLieux->cout_reparations, 0, ',', ' ') }} FCFA
        </div>
    </div>
    @endif

    <!-- Photos -->
    @if($etatLieux->photos && count($etatLieux->photos) > 0)
    <div class="section">
        <div class="section-title">PHOTOS ANNEXÉES</div>
        <div style="padding: 10px; background-color: #f5f5f5;">
            <p style="margin-bottom: 8px;">
                <strong>Nombre de photos jointes :</strong> {{ count($etatLieux->photos) }}
            </p>
            <p style="font-size: 9pt; color: #666;">
                Les photos sont disponibles dans la version numérique de ce document.
            </p>
        </div>
    </div>
    @endif

    <!-- Signatures -->
    <div class="signature-section">
        <div class="section-title">SIGNATURES</div>
        
        <div class="signature-box">
            <div class="signature-label">Le Propriétaire (Bailleur)</div>
            @if($etatLieux->date_signature_proprietaire)
                <div class="signature-date">
                    Signé le {{ $etatLieux->date_signature_proprietaire->format('d/m/Y à H:i') }}
                </div>
            @else
                <div style="margin-top: 10px; color: #e74c3c; font-style: italic;">
                    En attente de signature
                </div>
            @endif
            <div class="signature-name">
                {{ $contrat->proprietaire->user->full_name }}
            </div>
        </div>

        <div class="signature-box">
            <div class="signature-label">Le Locataire (Preneur)</div>
            @if($etatLieux->date_signature_locataire)
                <div class="signature-date">
                    Signé le {{ $etatLieux->date_signature_locataire->format('d/m/Y à H:i') }}
                </div>
            @else
                <div style="margin-top: 10px; color: #e74c3c; font-style: italic;">
                    En attente de signature
                </div>
            @endif
            <div class="signature-name">
                {{ $contrat->locataire->user->full_name }}
            </div>
        </div>
    </div>

    <!-- Mention Légale -->
    <div style="margin-top: 25px; padding: 10px; background-color: #f8f9fa; border-left: 4px solid #6c757d;">
        <p style="font-size: 9pt; margin-bottom: 5px;">
            <strong>Mention légale :</strong>
        </p>
        <p style="font-size: 8pt; text-align: justify;">
            Les parties reconnaissent avoir pris connaissance de l'état des lieux ci-dessus et en acceptent les termes. 
            @if($etatLieux->type_etat === 'sortie')
                Le montant des réparations pourra être déduit de la caution versée lors de la signature du contrat.
            @endif
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }} - Wassou - Plateforme de Gestion Immobilière<br>
        État des Lieux {{ $etatLieux->type_etat === 'entree' ? 'd\'Entrée' : 'de Sortie' }} - Contrat {{ $contrat->numero_contrat }}
    </div>
</body>
</html>