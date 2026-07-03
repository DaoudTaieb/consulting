<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Relevé Fournisseur - {{ $fournisseur->nom }}</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #f53003;
            --background: #fdfdfc;
            --surface: #ffffff;
            --text: #1b1b18;
            --text-muted: #706f6c;
            --border: #e3e3e0;
            --blue: #2563eb;
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
        }

        body {
            margin: 0;
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, sans-serif;
            background-color: var(--background);
            color: var(--text);
            padding: max(1rem, env(safe-area-inset-top)) max(1rem, env(safe-area-inset-right)) max(1rem, env(safe-area-inset-bottom)) max(1rem, env(safe-area-inset-left));
        }
        @media (min-width: 640px) { body { padding: 2rem; } }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: var(--text-muted);
            text-decoration: none;
            margin-bottom: 2rem;
            font-weight: 500;
            transition: color 0.2s;
        }
        .back-link:hover { color: var(--primary); }
        .back-link i { margin-right: 0.5rem; width: 18px; }

        header {
            margin-bottom: 2rem;
        }

        h1 {
            font-size: 2rem;
            font-weight: 600;
            margin: 0;
            color: var(--text);
        }

        .supplier-info {
            color: var(--text-muted);
            margin-top: 0.5rem;
            display: flex;
            gap: 1.5rem;
        }

        /* Summary Bar Styling */
        .summary-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            background: var(--surface);
            padding: 1rem 1.5rem;
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .summary-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .summary-label { color: var(--text-muted); }
        .val-blue { color: var(--blue); }
        .text-red { color: var(--danger); font-weight: 700; }
        .text-green { color: var(--success); font-weight: 700; }
        .text-orange { color: var(--warning); font-weight: 700; }

        /* Table Styling */
        .table-container {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: #f8f8f7;
            padding: 0.75rem 1rem;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border);
            color: var(--text-muted);
        }

        td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.9rem;
        }

        tr:last-child td { border-bottom: none; }
        
        .text-right { text-align: right; }
        .font-bold { font-weight: 600; }
        
        .type-badge {
            font-size: 0.75rem;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            background: #f3f4f6;
            color: #374151;
            font-weight: 600;
        }
        .doc-link {
            color: var(--blue);
            text-decoration: none;
            font-weight: 600;
        }
        .doc-link:hover {
            text-decoration: underline;
        }

        /* Responsive Adjustments */
        @media (max-width: 900px) {
            .summary-bar {
                gap: 1rem;
                padding: 1rem;
            }
            .summary-item {
                flex: 1 1 calc(50% - 1rem);
                min-width: 140px;
                justify-content: space-between;
                border-bottom: 1px solid #f0f0f0;
                padding-bottom: 0.5rem;
            }
            .summary-item:last-child { border-bottom: none; }
        }

        @media (max-width: 768px) {
            body { padding: 1rem; }
            header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1.5rem;
            }
            .btn-print { width: 100%; justify-content: center; }
            
            .table-container {
                overflow-x: auto;
            }
            table { min-width: 800px; }
            
            .details-wrapper {
                margin: 0.5rem;
                padding: 1rem !important;
            }
        }

        @media (max-width: 480px) {
            h1 { font-size: 1.5rem; }
            .supplier-info { flex-direction: column; gap: 0.25rem; font-size: 0.85rem; }
            .summary-item { flex: 1 1 100%; }
        }

        /* Details Row Styling */
        .details-row {
            background-color: #fcfcfc;
        }

        .details-row td {
            padding: 0 !important;
            border-bottom: 2px solid var(--border);
        }

        .details-wrapper {
            border-left: 4px solid var(--primary);
            margin: 0.5rem 1rem 1rem 1rem;
            border-radius: 0 8px 8px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--text);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .btn-print:hover { opacity: 0.9; }

        header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 2rem;
        }

        @media print {
            .back-link, .btn-print { display: none !important; }
            body { padding: 0; }
            .container { max-width: 100%; margin: 0; }
            .summary-bar { break-inside: avoid; }
            .details-row { break-inside: avoid; }
            .table-container { border: none; box-shadow: none; }
            th { background: #eee !important; color: black !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="back-link">
            <i data-lucide="arrow-left"></i> Retour à la liste
        </a>

        <header>
            <div>
                <h1>Relevé de compte</h1>
                <div class="supplier-info">
                    <span><strong>Fournisseur:</strong> {{ $fournisseur->nom }}</span>
                    @if($fournisseur->fournisseurcode)
                        <span><strong>Code:</strong> {{ $fournisseur->fournisseurcode }}</span>
                    @endif
                    @if($fournisseur->mf)
                        <span><strong>MF:</strong> {{ $fournisseur->mf }}</span>
                    @endif
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button onclick="window.print()" class="btn-print">
                    <i data-lucide="printer"></i> Imprimer
                </button>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-print" style="background: var(--surface); color: var(--text-muted); border: 1px solid var(--border);">
                        <i data-lucide="log-out"></i>
                    </button>
                </form>
            </div>
        </header>

        <div class="summary-bar">
            <div class="summary-item">
                <span class="summary-label">Solde Départ</span>
                <span class="summary-value {{ $summary['solde_depart'] > 0 ? 'text-red' : ($summary['solde_depart'] < 0 ? 'text-green' : 'text-orange') }}">
                    {{ number_format($summary['solde_depart'], 3, ',', ' ') }}
                </span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Solde Final</span>
                <span class="summary-value {{ $summary['solde_final'] > 0 ? 'text-red' : ($summary['solde_final'] < 0 ? 'text-green' : 'text-orange') }}">
                    {{ number_format($summary['solde_final'], 3, ',', ' ') }}
                </span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Factures</span>
                <span class="summary-value {{ $summary['factures'] > 0 ? 'text-red' : ($summary['factures'] < 0 ? 'text-green' : 'text-orange') }}">
                    {{ number_format($summary['factures'], 3, ',', ' ') }}
                </span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Bon Entrée</span>
                <span class="summary-value {{ $summary['bons_entree'] > 0 ? 'text-red' : ($summary['bons_entree'] < 0 ? 'text-green' : 'text-orange') }}">
                    {{ number_format($summary['bons_entree'], 3, ',', ' ') }}
                </span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Règlements</span>
                <span class="summary-value {{ $summary['reglements'] > 0 ? 'text-red' : ($summary['reglements'] < 0 ? 'text-green' : 'text-orange') }}">
                    {{ number_format($summary['reglements'], 3, ',', ' ') }}
                </span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Bon Sortie</span>
                <span class="summary-value {{ $summary['bons_sortie'] > 0 ? 'text-red' : ($summary['bons_sortie'] < 0 ? 'text-green' : 'text-orange') }}">
                    {{ number_format($summary['bons_sortie'], 3, ',', ' ') }}
                </span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Avoirs</span>
                <span class="summary-value {{ $summary['avoirs'] > 0 ? 'text-red' : ($summary['avoirs'] < 0 ? 'text-green' : 'text-orange') }}">
                    {{ number_format($summary['avoirs'], 3, ',', ' ') }}
                </span>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Libellé</th>
                        <th>Pièce N°</th>
                        <th class="text-right">Débit</th>
                        <th class="text-right">Crédit</th>
                        <th class="text-right">Solde</th>
                    </tr>
                </thead>
                <tbody>
                    @php $runningSolde = $summary['solde_depart']; @endphp
                    <tr>
                        <td>-</td>
                        <td class="font-bold">SOLDE INITIAL</td>
                        <td>-</td>
                        <td class="text-right">-</td>
                        <td class="text-right">-</td>
                        <td class="text-right font-bold {{ $runningSolde > 0 ? 'text-red' : ($runningSolde < 0 ? 'text-green' : 'text-orange') }}">
                            {{ number_format($runningSolde, 3, ',', ' ') }}
                        </td>
                    </tr>
                    @foreach($movements as $m)
                        @php 
                            $runningSolde = $runningSolde + $m->credit - $m->debit;
                            $hasDetails = isset($m->details) && count($m->details) > 0;
                        @endphp
                        <tr class="movement-row {{ $hasDetails ? 'has-details' : '' }}" id="row-{{ $m->type_slug }}-{{ $m->doc_id }}">
                            <td>{{ date('d/m/Y', strtotime($m->date)) }}</td>
                            <td><span class="type-badge">{{ $m->libelle }}</span></td>
                            <td>
                                @if(isset($m->type_slug) && $m->type_slug != 'facture' && $m->type_slug != 'reglement' && $m->type_slug != 'avoir')
                                    <a href="/{{ $m->type_slug }}/{{ $m->doc_id }}" class="doc-link">#{{ $m->numero }}</a>
                                @else
                                    #{{ $m->numero }}
                                @endif
                            </td>
                            <td class="text-right">{{ $m->debit > 0 ? number_format($m->debit, 3, ',', ' ') : '-' }}</td>
                            <td class="text-right">{{ $m->credit > 0 ? number_format($m->credit, 3, ',', ' ') : '-' }}</td>
                            <td class="text-right font-bold {{ $runningSolde > 0 ? 'text-red' : ($runningSolde < 0 ? 'text-green' : 'text-orange') }}">
                                {{ number_format($runningSolde, 3, ',', ' ') }}
                            </td>
                        </tr>
                        @if($hasDetails)
                            <tr class="details-row">
                                <td colspan="6">
                                    @include('partials.details_bon_content', [
                                        'type' => $m->libelle, 
                                        'bon' => $m->header, 
                                        'details' => $m->details, 
                                        'numero' => $m->numero,
                                        'is_mobile' => true
                                    ])
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
