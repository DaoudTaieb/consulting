<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Détails {{ $type }} #{{ $numero }}</title>
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
            cursor: pointer;
        }

        .back-link:hover {
            color: var(--primary);
        }

        .back-link i {
            margin-right: 0.5rem;
            width: 18px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border);
        }

        h1 {
            font-size: 1.75rem;
            font-weight: 600;
            margin: 0;
            color: var(--text);
        }

        .document-meta {
            margin-top: 0.5rem;
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .supplier-box {
            background: #f9fafb;
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .supplier-box h3 {
            margin: 0 0 0.5rem 0;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
        }

        .supplier-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text);
        }

        /* Table Styling */
        .table-container {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: #f8f8f7;
            padding: 1rem;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border);
            color: var(--text-muted);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.95rem;
        }

        /* Totals section */
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        .totals-box {
            width: 300px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .total-row:last-child {
            margin-bottom: 0;
            padding-top: 0.75rem;
            border-top: 1px solid var(--border);
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--primary);
        }

        .text-right {
            text-align: right;
        }

        .font-mono {
            font-family: monospace;
        }
    </style>
</head>

<body>
    <div class="container">
        <a onclick="window.history.back()" class="back-link">
            <i data-lucide="arrow-left"></i> Retour
        </a>

        <header>
            <div>
                <h1>{{ $type }} #{{ $numero }} ({{ $bon->numerointerne ?: '---' }})</h1>
                <div class="document-meta">
                    <span>Date:
                        <strong>{{ date('d/m/Y', strtotime($bon->datecreation ?? $bon->fbldate ?? $bon->fbrdate)) }}</strong></span>
                </div>
            </div>
            <div class="supplier-box">
                <h3>Fournisseur</h3>
                <div class="supplier-name">{{ $bon->fournisseur_nom }}</div>
            </div>
        </header>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Désignation</th>
                        <th class="text-right">Qte</th>
                        <th class="text-right">P.U HT</th>
                        <th class="text-right">P.U TTC</th>
                        <th class="text-right">Rem (%)</th>
                        <th class="text-right">Total HT</th>
                        <th class="text-right">Total TTC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $d)
                        <tr>
                            <td class="font-mono">{{ $d->produitcode }}</td>
                            <td>{{ $d->produitlibelle }}</td>
                            <td class="text-right">{{ number_format($d->qte, 2, ',', ' ') }}</td>
                            <td class="text-right">{{ number_format($d->ht, 3, ',', ' ') }}</td>
                            <td class="text-right">{{ number_format($d->ttc, 3, ',', ' ') }}</td>
                            <td class="text-right">{{ number_format($d->remise, 2, ',', ' ') }}%</td>
                            <td class="text-right">{{ number_format($d->totalhtnet, 3, ',', ' ') }}</td>
                            <td class="text-right">{{ number_format($d->totalttc, 3, ',', ' ') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="totals-section">
            <div class="totals-box">
                <div class="total-row">
                    <span>Total HT</span>
                    <span>{{ number_format($bon->totalhtnet ?? $bon->totalnetht ?? 0, 3, ',', ' ') }} TND</span>
                </div>
                <div class="total-row">
                    <span>Total TVA</span>
                    <span>{{ number_format($bon->totaltva, 3, ',', ' ') }} TND</span>
                </div>
                @if(($bon->droittimbre ?? 0) > 0)
                    <div class="total-row">
                        <span>Timbre</span>
                        <span>{{ number_format($bon->droittimbre ?? 0, 3, ',', ' ') }} TND</span>
                    </div>
                @endif
                <div class="total-row">
                    <span>NET A PAYER</span>
                    <span>{{ number_format($bon->netapayer, 3, ',', ' ') }} TND</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>