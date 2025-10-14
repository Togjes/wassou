<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrat de Location - <?php echo e($contrat->numero_contrat); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2c3e50;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 24pt;
            margin-bottom: 10px;
        }

        .header .numero {
            color: #e74c3c;
            font-size: 14pt;
            font-weight: bold;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
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

        .partie {
            background-color: #ecf0f1;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #3498db;
        }

        .partie-title {
            font-size: 13pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .bien-info {
            background-color: #e8f5e9;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #4caf50;
        }

        .montant {
            font-size: 13pt;
            font-weight: bold;
            color: #e74c3c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th {
            background-color: #34495e;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11pt;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }

        .signature-box {
            display: inline-block;
            width: 48%;
            vertical-align: top;
            padding: 15px;
            border: 2px solid #2c3e50;
            min-height: 120px;
        }

        .signature-box:first-child {
            margin-right: 3%;
        }

        .signature-label {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .signature-name {
            font-weight: bold;
            margin-top: 50px;
            border-top: 1px solid #000;
            padding-top: 5px;
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #2c3e50;
            text-align: center;
            font-size: 9pt;
            color: #777;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-actif { background-color: #27ae60; color: white; }
        .status-brouillon { background-color: #95a5a6; color: white; }
        .status-en_attente { background-color: #f39c12; color: white; }
        .status-resilie { background-color: #e74c3c; color: white; }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .clause {
            margin-bottom: 15px;
            text-align: justify;
        }

        .clause-title {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        @page {
            margin: 20mm;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>CONTRAT DE LOCATION</h1>
        <div class="numero"><?php echo e($contrat->numero_contrat); ?></div>
        <div style="margin-top: 10px;">
            Date d'établissement : <?php echo e($contrat->date_etablissement->format('d/m/Y')); ?>

        </div>
        <div style="margin-top: 5px;">
            <span class="status-badge status-<?php echo e($contrat->statut); ?>">
                <?php echo e(strtoupper(str_replace('_', ' ', $contrat->statut))); ?>

            </span>
        </div>
    </div>

    <!-- Parties du Contrat -->
    <div class="section">
        <div class="section-title">LES PARTIES</div>

        <!-- Propriétaire -->
        <div class="partie">
            <div class="partie-title">LE BAILLEUR (Propriétaire)</div>
            <div class="info-row">
                <div class="info-label">Nom complet :</div>
                <div class="info-value"><?php echo e($contrat->proprietaire->user->full_name); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Email :</div>
                <div class="info-value"><?php echo e($contrat->proprietaire->user->email); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Téléphone :</div>
                <div class="info-value"><?php echo e($contrat->proprietaire->user->phone); ?></div>
            </div>
            <?php if($contrat->proprietaire->adresse): ?>
            <div class="info-row">
                <div class="info-label">Adresse :</div>
                <div class="info-value"><?php echo e($contrat->proprietaire->adresse); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Locataire -->
        <div class="partie">
            <div class="partie-title">LE PRENEUR (Locataire)</div>
            <div class="info-row">
                <div class="info-label">Nom complet :</div>
                <div class="info-value"><?php echo e($contrat->locataire->user->full_name); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Email :</div>
                <div class="info-value"><?php echo e($contrat->locataire->user->email); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Téléphone :</div>
                <div class="info-value"><?php echo e($contrat->locataire->user->phone); ?></div>
            </div>
            <?php if($contrat->locataire->profession): ?>
            <div class="info-row">
                <div class="info-label">Profession :</div>
                <div class="info-value"><?php echo e($contrat->locataire->profession); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Démarcheur si présent -->
        <?php if($contrat->demarcheur): ?>
        <div class="partie">
            <div class="partie-title">LE DÉMARCHEUR</div>
            <div class="info-row">
                <div class="info-label">Nom complet :</div>
                <div class="info-value"><?php echo e($contrat->demarcheur->user->full_name); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Téléphone :</div>
                <div class="info-value"><?php echo e($contrat->demarcheur->user->phone); ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Bien Loué -->
    <div class="section">
        <div class="section-title">LE BIEN LOUÉ</div>
        <div class="bien-info">
            <div class="info-row">
                <div class="info-label">Bien immobilier :</div>
                <div class="info-value"><strong><?php echo e($contrat->chambre->bien->titre); ?></strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Type :</div>
                <div class="info-value"><?php echo e(ucfirst($contrat->chambre->bien->type_bien)); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Adresse :</div>
                <div class="info-value"><?php echo e($contrat->chambre->bien->quartier); ?>, <?php echo e($contrat->chambre->bien->ville); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Chambre :</div>
                <div class="info-value"><strong><?php echo e($contrat->chambre->nom_chambre); ?></strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Type de chambre :</div>
                <div class="info-value"><?php echo e(ucfirst(str_replace('_', ' ', $contrat->chambre->type_chambre))); ?></div>
            </div>
            <?php if($contrat->chambre->surface_m2): ?>
            <div class="info-row">
                <div class="info-label">Surface :</div>
                <div class="info-value"><?php echo e($contrat->chambre->surface_m2); ?> m²</div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Conditions Financières -->
    <div class="section">
        <div class="section-title">CONDITIONS FINANCIÈRES</div>
        
        <div class="info-row">
            <div class="info-label">Loyer mensuel :</div>
            <div class="info-value montant"><?php echo e(number_format($contrat->chambre->loyer_mensuel, 0, ',', ' ')); ?> FCFA</div>
        </div>
        <div class="info-row">
            <div class="info-label">Jour de paiement :</div>
            <div class="info-value">Le <?php echo e($contrat->date_paiement_loyer); ?> de chaque mois</div>
        </div>

        <?php if($contrat->avances->count() > 0): ?>
        <div class="mt-20">
            <strong>Paiements initiaux :</strong>
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Montant Initial</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $contrat->avances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $avance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e(ucfirst(str_replace('_', ' ', $avance->type_avance))); ?></td>
                        <td><?php echo e(number_format($avance->montant_initial, 0, ',', ' ')); ?> FCFA</td>
                        <td><?php echo e(ucfirst($avance->statut)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Clauses du Contrat -->
    <div class="section">
        <div class="section-title">CLAUSES DU CONTRAT</div>

        <div class="clause">
            <div class="clause-title">Article 1 - Objet du contrat</div>
            Le bailleur loue au preneur le bien immobilier décrit ci-dessus, qui déclare bien le connaître et l'accepter en l'état.
        </div>

        <div class="clause">
            <div class="clause-title">Article 2 - Loyer</div>
            Le loyer mensuel est fixé à <?php echo e(number_format($contrat->chambre->loyer_mensuel, 0, ',', ' ')); ?> FCFA et doit être payé le <?php echo e($contrat->date_paiement_loyer); ?> de chaque mois.
        </div>

        <div class="clause">
            <div class="clause-title">Article 3 - Dépôt de garantie</div>
            <?php if($contrat->avances->where('type_avance', 'caution')->first()): ?>
            Un dépôt de garantie de <?php echo e(number_format($contrat->avances->where('type_avance', 'caution')->first()->montant_initial, 0, ',', ' ')); ?> FCFA a été versé par le preneur. Ce dépôt sera restitué en fin de bail, déduction faite des éventuelles réparations nécessaires.
            <?php else: ?>
            Aucun dépôt de garantie n'a été exigé pour cette location.
            <?php endif; ?>
        </div>

        <div class="clause">
            <div class="clause-title">Article 4 - Entretien et réparations</div>
            Le preneur s'engage à entretenir les lieux loués en bon état et à effectuer les réparations locatives. Les grosses réparations restent à la charge du bailleur.
        </div>

        <div class="clause">
            <div class="clause-title">Article 5 - Résiliation</div>
            Chaque partie peut résilier le contrat en respectant un préavis conforme à la législation en vigueur.
        </div>
    </div>

    <!-- Signatures -->
    <div class="signature-section">
        <div class="section-title">SIGNATURES</div>
        
        <div class="signature-box">
            <div class="signature-label">Le Bailleur</div>
            <?php if($contrat->date_signature_proprietaire): ?>
                <div style="margin-top: 10px;">
                    Signé le <?php echo e($contrat->date_signature_proprietaire->format('d/m/Y à H:i')); ?>

                </div>
            <?php else: ?>
                <div style="margin-top: 10px; color: #e74c3c;">
                    En attente de signature
                </div>
            <?php endif; ?>
            <div class="signature-name">
                <?php echo e($contrat->proprietaire->user->full_name); ?>

            </div>
        </div>

        <div class="signature-box">
            <div class="signature-label">Le Preneur</div>
            <?php if($contrat->date_signature_locataire): ?>
                <div style="margin-top: 10px;">
                    Signé le <?php echo e($contrat->date_signature_locataire->format('d/m/Y à H:i')); ?>

                </div>
            <?php else: ?>
                <div style="margin-top: 10px; color: #e74c3c;">
                    En attente de signature
                </div>
            <?php endif; ?>
            <div class="signature-name">
                <?php echo e($contrat->locataire->user->full_name); ?>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Document généré le <?php echo e(now()->format('d/m/Y à H:i')); ?> - Wassou - Plateforme de Gestion Immobilière
    </div>
</body>
</html><?php /**PATH C:\wamp64\www\projets\starter-kit\resources\views/pdf/contrat.blade.php ENDPATH**/ ?>