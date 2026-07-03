<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bonjour Taieb</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #f53003;
            --background: #fdfdfc;
            --surface: #ffffff;
            --text: #1b1b18;
            --text-muted: #706f6c;
            --border: #e3e3e0;
            --success: #10b981;
            --danger: #ef4444;
        }

        body {
            margin: 0;
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, sans-serif;
            background-color: var(--background);
            color: var(--text);
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            margin-bottom: 2rem;
            text-align: center;
        }

        h1 {
            font-size: 3rem;
            font-weight: 600;
            margin: 0;
            color: var(--primary);
        }

        header p {
            color: var(--text-muted);
            font-size: 1.2rem;
        }

        .table-container {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
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
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.95rem;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: #fafaf9;
        }

        .bold {
            font-weight: 600;
        }

        .text-right {
            text-align: right;
        }

        .text-red {
            color: var(--danger);
            font-weight: 600;
        }

        .text-green {
            color: var(--success);
            font-weight: 600;
        }

        .text-orange {
            color: #f59e0b;
            font-weight: 600;
        }

        .badge {
            background: #eee;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-family: monospace;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .famille-tag {
            display: block;
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: bold;
            margin-top: 0.2rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--primary);
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 2px 4px rgba(245, 48, 3, 0.2);
        }

        .btn i {
            width: 18px;
            height: 18px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            h1 {
                font-size: 2rem;
            }

            header p {
                font-size: 1rem;
            }

            .table-container {
                overflow-x: auto;
                border-radius: 8px;
            }

            table {
                min-width: 800px;
                /* Force scroll on mobile to keep table readable */
            }

            th,
            td {
                padding: 0.75rem;
                font-size: 0.85rem;
            }

            .badge {
                font-size: 0.75rem;
                padding: 0.2rem 0.4rem;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div style="flex: 1;"></div>
            <div style="text-align: center; flex: 2;">
                <h1>bonjourtaieb</h1>
                <p>Liste des Fournisseurs ({{ count($fournisseurs) }})</p>
            </div>
            <div style="flex: 1; text-align: right;">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn" title="Se déconnecter" style="color: var(--text-muted);">
                        <i data-lucide="log-out"></i>
                        <span style="margin-left: 0.5rem; font-weight: 600;">Déconnexion</span>
                    </button>
                </form>
            </div>
        </header>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Fournisseur</th>
                        <th>Matricule Fiscale</th>
                        <th>Téléphone</th>
                        <th>Adresse</th>
                        <th>Solde</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fournisseurs as $f)
                        <tr>
                            <td><span class="badge">{{ $f->fournisseurcode ?? $f->fournisseurid }}</span></td>
                            <td class="font-bold">
                                {{ $f->nom }}
                                @if($f->famille)
                                    <span class="famille-tag">{{ $f->famille }}</span>
                                @endif
                            </td>
                            <td>{{ $f->mf }}</td>
                            <td>{{ $f->tel }}</td>
                            <td>{{ $f->adressefacturation ?? $f->ville }}</td>
                            <td
                                class="text-right {{ $f->solde > 0 ? 'text-red' : ($f->solde < 0 ? 'text-green' : 'text-orange') }}">
                                {{ number_format($f->solde, 3, ',', ' ') }} TND
                            </td>
                            <td class="text-right">
                                <a href="/fournisseur/{{ $f->fournisseurid }}/releve" class="btn" title="Voir le relevé">
                                    <i data-lucide="file-text"></i>
                                </a>
                            </td>
                        </tr>
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