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
            font-family: "Inter", sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #000;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #000;
        }

        .header h1 {
            font-size: 20pt;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .header .numero {
            font-size: 12pt;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            padding: 8px 0;
            font-size: 12pt;
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 1px solid #000;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 6px;
        }

        .info-label {
            display: table-cell;
            width: 35%;
            font-weight: 500;
        }

        .info-value {
            display: table-cell;
            width: 65%;
        }

        .partie {
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .partie-title {
            font-size: 11pt;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .bien-info {
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .montant {
            font-size: 11pt;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th {
            background-color: #f0f0f0;
            padding: 8px;
            text-align: left;
            font-size: 10pt;
            font-weight: 600;
            border: 1px solid #ddd;
        }

        table td {
            padding: 8px;
            border: 1px solid #ddd;
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
            border: 1px solid #000;
            min-height: 100px;
        }

        .signature-box:first-child {
            margin-right: 3%;
        }

        .signature-label {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .signature-name {
            font-weight: 600;
            margin-top: 40px;
            border-top: 1px solid #000;
            padding-top: 5px;
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 9pt;
            color: #555;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: 600;
            text-transform: uppercase;
            border: 1px solid #000;
        }

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
            margin-bottom: 12px;
            text-align: justify;
        }

        .clause-title {
            font-weight: 600;
            margin-bottom: 4px;
        }

        @page {
            margin: 15mm;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>CONTRAT DE LOCATION</h1>
        <div class="numero"><?php echo e($contrat->numero_contrat); ?></div>
        <div style="margin-top: 8px;">
            Date d'établissement : <?php echo e($contrat->date_etablissement->format('d/m/Y')); ?>

        </div>
        <div style="margin-top: 5px;">
            <span class="status-badge">
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
                <div style="margin-top: 8px;">
                    Signé le <?php echo e($contrat->date_signature_proprietaire->format('d/m/Y à H:i')); ?>

                </div>
            <?php else: ?>
                <div style="margin-top: 8px;">
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
                <div style="margin-top: 8px;">
                    Signé le <?php echo e($contrat->date_signature_locataire->format('d/m/Y à H:i')); ?>

                </div>
            <?php else: ?>
                <div style="margin-top: 8px;">
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