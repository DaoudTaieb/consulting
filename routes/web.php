<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginController;
use Inertia\Inertia;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/logo', function () {
    $societe = DB::table('societes')->first();
    
    if (!$societe || !$societe->logo) {
        if (file_exists(public_path('logo.jpg'))) {
            return response()->file(public_path('logo.jpg'));
        }
        return response('', 404);
    }

    $logoData = $societe->logo;
    if (is_resource($logoData)) {
        $logoData = stream_get_contents($logoData);
    }

    try {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($logoData);
    } catch (\Exception $e) {
        $mimeType = 'image/jpeg';
    }

    return response($logoData)->header('Content-Type', $mimeType);
});

Route::get('/manifest.json', function () {
    $societe = DB::table('societes')->first();
    $appName = $societe && $societe->raison ? $societe->raison : config('app.name', 'Application');
    
    return response()->json([
        'name' => $appName,
        'short_name' => $appName,
        'start_url' => '/',
        'display' => 'standalone',
        'background_color' => '#fdfdfc',
        'theme_color' => '#b45309',
        'description' => $societe && $societe->activite ? $societe->activite : 'Application',
        'icons' => [
            [
                'src' => '/logo',
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any maskable'
            ]
        ]
    ]);
});

Route::middleware('auth')->group(function () {

    Route::get('/', function (Request $request) {
        $defaultRoute = $request->cookie('gfm_default_route', '/statistiques');
        return redirect($defaultRoute);
    });

    Route::get('/parametres', fn () => Inertia::render('Settings'))->name('settings');

    // ─── Dashboard Global ───────────────────────────────
    Route::get('/tableau-de-bord', function () {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // KPIs ce mois
        $caVentesMois = DB::table('cfactures')->whereBetween('cfacturedate', [$startOfMonth, $now])->sum('totalttc');
        $caAchatsMois = DB::table('ffactures')->whereBetween('ffacturedate', [$startOfMonth, $now])->sum('totalttc');
        $encaissementsMois = DB::table('creglements')->whereBetween('date', [$startOfMonth, $now])->sum('montant');
        $decaissementsMois = DB::table('freglements')->whereBetween('date', [$startOfMonth, $now])->sum('montant');
        $depensesMois = DB::table('depenses')->whereBetween('date', [$startOfMonth, $now])->sum('montant');

        // KPIs mois dernier (pour comparaison)
        $caVentesLastMois = DB::table('cfactures')->whereBetween('cfacturedate', [$startOfLastMonth, $endOfLastMonth])->sum('totalttc');
        $caAchatsLastMois = DB::table('ffactures')->whereBetween('ffacturedate', [$startOfLastMonth, $endOfLastMonth])->sum('totalttc');
        $depensesLastMois = DB::table('depenses')->whereBetween('date', [$startOfLastMonth, $endOfLastMonth])->sum('montant');

        // Totaux globaux
        $totalCreancesClients = DB::table('clients')->sum('solde');
        $totalDettesFournisseurs = DB::table('fournisseurs')->sum('solde');
        $nbClients = DB::table('clients')->count();
        $nbFournisseurs = DB::table('fournisseurs')->count();
        $nbProduits = DB::table('produits')->count();

        // Top 5 clients par solde
        $topClients = DB::table('clients')
            ->where('solde', '>', 0)
            ->orderBy('solde', 'desc')
            ->limit(5)
            ->select('clientid', 'nom', 'solde')
            ->get();

        // Top 5 fournisseurs par solde
        $topFournisseurs = DB::table('fournisseurs')
            ->where('solde', '>', 0)
            ->orderBy('solde', 'desc')
            ->limit(5)
            ->select('fournisseurid', 'nom', 'solde')
            ->get();

        // CA mensuel sur 12 mois
        $caMensuel = DB::table('cfactures')
            ->selectRaw("TO_CHAR(cfacturedate, 'YYYY-MM') as mois, SUM(totalttc) as total")
            ->where('cfacturedate', '>=', $now->copy()->subMonths(12)->startOfMonth())
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        // Achats mensuels sur 12 mois
        $achatsMensuel = DB::table('ffactures')
            ->selectRaw("TO_CHAR(ffacturedate, 'YYYY-MM') as mois, SUM(totalttc) as total")
            ->where('ffacturedate', '>=', $now->copy()->subMonths(12)->startOfMonth())
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        // Dépenses par famille
        $depensesParFamille = DB::table('depenses')
            ->leftJoin('familledepenses', 'depenses.familledepenseid', '=', 'familledepenses.familledepenseid')
            ->selectRaw("COALESCE(familledepenses.libelle, 'Autre') as famille, SUM(depenses.montant) as total")
            ->groupBy('famille')
            ->orderByDesc('total')
            ->get();

        return Inertia::render('DashboardGlobal', [
            'kpis' => [
                'ca_ventes_mois' => round(floatval($caVentesMois), 3),
                'ca_achats_mois' => round(floatval($caAchatsMois), 3),
                'encaissements_mois' => round(floatval($encaissementsMois), 3),
                'decaissements_mois' => round(floatval($decaissementsMois), 3),
                'depenses_mois' => round(floatval($depensesMois), 3),
                'ca_ventes_last_mois' => round(floatval($caVentesLastMois), 3),
                'ca_achats_last_mois' => round(floatval($caAchatsLastMois), 3),
                'depenses_last_mois' => round(floatval($depensesLastMois), 3),
                'total_creances_clients' => round(floatval($totalCreancesClients), 3),
                'total_dettes_fournisseurs' => round(floatval($totalDettesFournisseurs), 3),
                'nb_clients' => $nbClients,
                'nb_fournisseurs' => $nbFournisseurs,
                'nb_produits' => $nbProduits,
            ],
            'topClients' => $topClients,
            'topFournisseurs' => $topFournisseurs,
            'caMensuel' => $caMensuel,
            'achatsMensuel' => $achatsMensuel,
            'depensesParFamille' => $depensesParFamille,
        ]);
    })->name('tableau-de-bord');

    // ─── Dépenses ───────────────────────────────────────
    Route::get('/depenses', function (Request $request) {
        $query = DB::table('depenses')
            ->leftJoin('familledepenses', 'depenses.familledepenseid', '=', 'familledepenses.familledepenseid')
            ->leftJoin('modereglements', 'depenses.modereglementid', '=', 'modereglements.modereglementid')
            ->select(
                'depenses.*',
                'familledepenses.libelle as famille',
                'modereglements.libelle as mode_reglement'
            );

        if ($request->has('famille') && $request->famille) {
            $query->where('depenses.familledepenseid', $request->famille);
        }
        if ($request->has('date_debut') && $request->date_debut) {
            $query->where('depenses.date', '>=', $request->date_debut);
        }
        if ($request->has('date_fin') && $request->date_fin) {
            $query->where('depenses.date', '<=', $request->date_fin . ' 23:59:59');
        }
        if ($request->has('search') && $request->search) {
            $s = '%' . $request->search . '%';
            $query->where(function($q) use ($s) {
                $q->where('depenses.libelle', 'ILIKE', $s)
                  ->orWhere('depenses.benificiaire', 'ILIKE', $s)
                  ->orWhere('depenses.numero', 'ILIKE', $s);
            });
        }

        $depenses = $query->orderBy('depenses.date', 'desc')->get();
        $familles = DB::table('familledepenses')->orderBy('libelle')->get();

        // Totaux par famille pour le graphique
        $totauxParFamille = DB::table('depenses')
            ->leftJoin('familledepenses', 'depenses.familledepenseid', '=', 'familledepenses.familledepenseid')
            ->selectRaw("COALESCE(familledepenses.libelle, 'Autre') as famille, SUM(depenses.montant) as total, COUNT(*) as nb");

        if ($request->has('date_debut') && $request->date_debut) {
            $totauxParFamille->where('depenses.date', '>=', $request->date_debut);
        }
        if ($request->has('date_fin') && $request->date_fin) {
            $totauxParFamille->where('depenses.date', '<=', $request->date_fin . ' 23:59:59');
        }

        $totauxParFamille = $totauxParFamille->groupBy('famille')->orderByDesc('total')->get();

        return Inertia::render('Depenses', [
            'depenses' => $depenses,
            'familles' => $familles,
            'totauxParFamille' => $totauxParFamille,
            'filters' => $request->only(['famille', 'date_debut', 'date_fin', 'search']),
        ]);
    })->name('depenses');

    // ─── Consulting: Trésorerie & Échéancier ─────────────────
    Route::get('/tresorerie', function () {
        $banques = DB::table('agencebs')->orderBy('libelle')->get();
        
        $echeancesClients = DB::table('creglements')
            ->join('clients', 'creglements.clientid', '=', 'clients.clientid')
            ->leftJoin('modereglements', 'creglements.modereglementid', '=', 'modereglements.modereglementid')
            ->leftJoin('statusreglements', 'creglements.statusreglementid', '=', 'statusreglements.statusreglementid')
            ->where('creglements.echeance', '>=', now())
            ->whereIn('creglements.statusreglementid', [1, 5]) // En cours, Préavisé
            ->select('creglements.date', 'creglements.echeance', 'creglements.montant', 'clients.nom as tiers', 'modereglements.libelle as mode', 'statusreglements.libelle as statut', DB::raw("'client' as type"))
            ->orderBy('creglements.echeance')
            ->get();
            
        $echeancesFournisseurs = DB::table('freglements')
            ->join('fournisseurs', 'freglements.fournisseurid', '=', 'fournisseurs.fournisseurid')
            ->leftJoin('modereglements', 'freglements.modereglementid', '=', 'modereglements.modereglementid')
            ->leftJoin('statusreglements', 'freglements.statusreglementid', '=', 'statusreglements.statusreglementid')
            ->where('freglements.echeance', '>=', now())
            ->whereIn('freglements.statusreglementid', [1, 5]) // En cours, Préavisé
            ->select('freglements.date', 'freglements.echeance', 'freglements.montant', 'fournisseurs.nom as tiers', 'modereglements.libelle as mode', 'statusreglements.libelle as statut', DB::raw("'fournisseur' as type"))
            ->orderBy('freglements.echeance')
            ->get();
            
        $impayesClients = DB::table('creglements')
            ->join('clients', 'creglements.clientid', '=', 'clients.clientid')
            ->leftJoin('modereglements', 'creglements.modereglementid', '=', 'modereglements.modereglementid')
            ->where('creglements.statusreglementid', 4) // Impayé
            ->select('creglements.date', 'creglements.echeance', 'creglements.montant', 'clients.nom as tiers', 'modereglements.libelle as mode', DB::raw("'client' as type"))
            ->orderBy('creglements.echeance', 'desc')
            ->get();
            
        $impayesFournisseurs = DB::table('freglements')
            ->join('fournisseurs', 'freglements.fournisseurid', '=', 'fournisseurs.fournisseurid')
            ->leftJoin('modereglements', 'freglements.modereglementid', '=', 'modereglements.modereglementid')
            ->where('freglements.statusreglementid', 4) // Impayé
            ->select('freglements.date', 'freglements.echeance', 'freglements.montant', 'fournisseurs.nom as tiers', 'modereglements.libelle as mode', DB::raw("'fournisseur' as type"))
            ->orderBy('freglements.echeance', 'desc')
            ->get();
            
        return Inertia::render('Tresorerie', [
            'banques' => $banques,
            'echeancesClients' => $echeancesClients,
            'echeancesFournisseurs' => $echeancesFournisseurs,
            'impayesClients' => $impayesClients,
            'impayesFournisseurs' => $impayesFournisseurs
        ]);
    })->name('tresorerie');

    // ─── Consulting: Performances Produits ───────────────────
    Route::get('/performances-produits', function () {
        $topCa = DB::table('detcfactures')
            ->join('produits', 'detcfactures.produitid', '=', 'produits.produitid')
            ->leftJoin('familles', 'produits.familleid', '=', 'familles.familleid')
            ->select('produits.produitcode', 'produits.produitlibelle', 'familles.famillelibelle', DB::raw('SUM(detcfactures.qte) as total_qte'), DB::raw('SUM(detcfactures.totalttc) as total_ca'))
            ->groupBy('produits.produitid', 'produits.produitcode', 'produits.produitlibelle', 'familles.famillelibelle')
            ->orderByDesc('total_ca')
            ->limit(50)
            ->get();
            
        $topQte = DB::table('detcfactures')
            ->join('produits', 'detcfactures.produitid', '=', 'produits.produitid')
            ->leftJoin('familles', 'produits.familleid', '=', 'familles.familleid')
            ->select('produits.produitcode', 'produits.produitlibelle', 'familles.famillelibelle', DB::raw('SUM(detcfactures.qte) as total_qte'), DB::raw('SUM(detcfactures.totalttc) as total_ca'))
            ->groupBy('produits.produitid', 'produits.produitcode', 'produits.produitlibelle', 'familles.famillelibelle')
            ->orderByDesc('total_qte')
            ->limit(50)
            ->get();
            
        $soldProduitsIds = DB::table('detcfactures')->pluck('produitid')->unique();
        
        $dormants = DB::table('stocks')
            ->join('produits', 'stocks.produitid', '=', 'produits.produitid')
            ->leftJoin('familles', 'produits.familleid', '=', 'familles.familleid')
            ->whereNotIn('stocks.produitid', $soldProduitsIds)
            ->where('stocks.qtestock', '>', 0)
            ->select('produits.produitcode', 'produits.produitlibelle', 'familles.famillelibelle', 'stocks.qtestock', 'stocks.valeurstockttc')
            ->orderByDesc('stocks.valeurstockttc')
            ->limit(50)
            ->get();
            
        return Inertia::render('Performances', [
            'topCa' => $topCa,
            'topQte' => $topQte,
            'dormants' => $dormants
        ]);
    })->name('performances-produits');

    Route::get('/dashboard', function () {
        $fournisseurs = DB::table('fournisseurs')
            ->leftJoin('fournisseurfamilles', 'fournisseurs.fournisseurfamilleid', '=', 'fournisseurfamilles.fournisseurfamilleid')
            ->select('fournisseurs.*', 'fournisseurfamilles.fournisseurfamillelibelle as famille')
            ->orderBy('fournisseurs.solde', 'desc')
            ->get();

        return Inertia::render('Dashboard', [
            'fournisseurs' => $fournisseurs
        ]);
    })->name('dashboard');

    Route::get('/fournisseurs/releves-globaux', function () {
        $fournisseurs = DB::table('fournisseurs')
            ->leftJoin('fournisseurfamilles', 'fournisseurs.fournisseurfamilleid', '=', 'fournisseurfamilles.fournisseurfamilleid')
            ->select('fournisseurs.*', 'fournisseurfamilles.fournisseurfamillelibelle as famille')
            ->orderBy('fournisseurs.nom')
            ->get();
            
        $fournisseursIds = $fournisseurs->pluck('fournisseurid')->toArray();

        if (empty($fournisseursIds)) {
            return Inertia::render('GlobalStatements', ['statements' => []]);
        }

        $factures = DB::table('ffactures')
            ->whereIn('fournisseurid', $fournisseursIds)
            ->select('fournisseurid', 'ffacturedate as date', DB::raw("'Facture' as libelle"), DB::raw("CAST(ffacturenumero AS TEXT) as numero"), DB::raw("0 as debit"), 'totalttc as credit', DB::raw("ffactureid as doc_id"), DB::raw("'facture' as type_slug"));

        $reglements = DB::table('freglements')
            ->whereIn('fournisseurid', $fournisseursIds)
            ->select('fournisseurid', 'date', DB::raw("'Règlement' as libelle"), DB::raw("CAST(numero AS TEXT) as numero"), 'montant as debit', DB::raw("0 as credit"), DB::raw("freglementid as doc_id"), DB::raw("'reglement' as type_slug"));

        $avoirs = DB::table('favoirs')
            ->whereIn('fournisseurid', $fournisseursIds)
            ->select('fournisseurid', 'favoirdate as date', DB::raw("'Avoir' as libelle"), DB::raw("CAST(favoirnumero AS TEXT) as numero"), 'totalttc as debit', DB::raw("0 as credit"), DB::raw("favoirid as doc_id"), DB::raw("'avoir' as type_slug"));

        $bls = DB::table('fbls')
            ->whereIn('fournisseurid', $fournisseursIds)
            ->where('transfere', false)
            ->select('fournisseurid', 'fbldate as date', DB::raw("'Bon Entrée' as libelle"), DB::raw("CAST(fblnumero AS TEXT) as numero"), DB::raw("0 as debit"), 'totalttc as credit', DB::raw("fblid as doc_id"), DB::raw("'bon-entree' as type_slug"));

        $brs = DB::table('fbrs')
            ->whereIn('fournisseurid', $fournisseursIds)
            ->where('transfere', false)
            ->select('fournisseurid', 'fbrdate as date', DB::raw("'Bon Sortie' as libelle"), DB::raw("CAST(fbrnumero AS TEXT) as numero"), 'totalttc as debit', DB::raw("0 as credit"), DB::raw("fbrid as doc_id"), DB::raw("'bon-sortie' as type_slug"));

        $movements = $factures->union($reglements)->union($avoirs)->union($bls)->union($brs)
            ->orderBy('date')
            ->get();

        $movementsGrouped = $movements->groupBy('fournisseurid');
        
        // Pre-fetch all necessary details for ALL suppliers in this report to avoid N+1
        $fblIds = $movements->where('type_slug', 'bon-entree')->pluck('doc_id')->unique()->toArray();
        $fbrIds = $movements->where('type_slug', 'bon-sortie')->pluck('doc_id')->unique()->toArray();
        $regIds = $movements->where('type_slug', 'reglement')->pluck('doc_id')->unique()->toArray();

        $blHeaders = !empty($fblIds) ? DB::table('fbls')->whereIn('fblid', $fblIds)->get()->keyBy('fblid') : collect();
        $brHeaders = !empty($fbrIds) ? DB::table('fbrs')->whereIn('fbrid', $fbrIds)->get()->keyBy('fbrid') : collect();
        $regDetails = !empty($regIds) ? DB::table('freglements')
            ->leftJoin('modereglements', 'freglements.modereglementid', '=', 'modereglements.modereglementid')
            ->whereIn('freglementid', $regIds)
            ->select('freglements.*', 'modereglements.libelle as mode_libelle')
            ->get()->keyBy('freglementid') : collect();

        $blDetails = !empty($fblIds) ? DB::table('detfbls')
            ->join('produits', 'detfbls.produitid', '=', 'produits.produitid')
            ->whereIn('fblid', $fblIds)
            ->select('detfbls.*', 'produits.produitlibelle', 'produits.produitcode')
            ->get()->groupBy('fblid') : collect();

        $brDetails = !empty($fbrIds) ? DB::table('detfbrs')
            ->join('produits', 'detfbrs.produitid', '=', 'produits.produitid')
            ->whereIn('fbrid', $fbrIds)
            ->select('detfbrs.*', 'produits.produitlibelle', 'produits.produitcode')
            ->get()->groupBy('fbrid') : collect();

        $result = [];
        
        foreach ($fournisseurs as $fournisseur) {
            $fMovements = $movementsGrouped->get($fournisseur->fournisseurid, collect([]));
            $soldeDepart = floatval($fournisseur->soldeinitial ?? 0);
            
            // if ($soldeDepart == 0 && $fMovements->isEmpty()) continue;

            $currentSolde = $soldeDepart;
            $fMovementsArray = [];
            
            $facturesTotal = 0; $reglementsTotal = 0; $avoirsTotal = 0;
            $bonsEntreeTotal = 0; $bonsSortieTotal = 0;

            foreach ($fMovements as $m) {
                $credit = floatval($m->credit);
                $debit = floatval($m->debit);
                
                $currentSolde += $credit;
                $currentSolde -= $debit;
                
                if ($m->type_slug === 'facture') $facturesTotal += $credit;
                if ($m->type_slug === 'reglement') $reglementsTotal += $debit;
                if ($m->type_slug === 'avoir') $avoirsTotal += $debit;
                if ($m->type_slug === 'bon-entree') $bonsEntreeTotal += $credit;
                if ($m->type_slug === 'bon-sortie') $bonsSortieTotal += $debit;

                $mArray = (array)$m;
                $mArray['solde_cumule'] = $currentSolde;
                
                // Attach details based on type
                if ($m->type_slug === 'bon-entree') {
                    $mArray['header'] = $blHeaders->get($m->doc_id);
                    $mArray['details'] = $blDetails->get($m->doc_id);
                } elseif ($m->type_slug === 'bon-sortie') {
                    $mArray['header'] = $brHeaders->get($m->doc_id);
                    $mArray['details'] = $brDetails->get($m->doc_id);
                } elseif ($m->type_slug === 'reglement') {
                    $mArray['extra'] = $regDetails->get($m->doc_id);
                }

                $fMovementsArray[] = $mArray;
            }

            $result[] = [
                'fournisseur' => $fournisseur,
                'movements' => $fMovementsArray,
                'summary' => [
                    'solde_depart' => $soldeDepart,
                    'factures' => $facturesTotal,
                    'reglements' => $reglementsTotal,
                    'avoirs' => $avoirsTotal,
                    'bons_entree' => $bonsEntreeTotal,
                    'bons_sortie' => $bonsSortieTotal,
                    'solde_final' => $currentSolde
                ]
            ];
        }

        return Inertia::render('GlobalStatements', [
            'statements' => $result
        ]);
    })->name('fournisseurs.releves-globaux');

    Route::get('/fournisseur/{id}/releve', function ($id) {
        $fournisseur = DB::table('fournisseurs')
            ->leftJoin('fournisseurfamilles', 'fournisseurs.fournisseurfamilleid', '=', 'fournisseurfamilles.fournisseurfamilleid')
            ->select('fournisseurs.*', 'fournisseurfamilles.fournisseurfamillelibelle as famille')
            ->where('fournisseurid', $id)
            ->first();

        if (!$fournisseur)
            abort(404);

        $factures = DB::table('ffactures')
            ->where('fournisseurid', $id)
            ->select('ffacturedate as date', DB::raw("CAST(ffacturedate AS DATE) as date_only"), DB::raw("'Facture' as libelle"), DB::raw("CAST(ffacturenumero AS TEXT) as numero"), DB::raw("0 as debit"), 'totalttc as credit', DB::raw("ffactureid as doc_id"), DB::raw("'facture' as type_slug"), DB::raw("1 as priority"));

        $reglements = DB::table('freglements')
            ->where('fournisseurid', $id)
            ->select('date', DB::raw("CAST(date AS DATE) as date_only"), DB::raw("'Règlement' as libelle"), DB::raw("CAST(numero AS TEXT) as numero"), 'montant as debit', DB::raw("0 as credit"), DB::raw("freglementid as doc_id"), DB::raw("'reglement' as type_slug"), DB::raw("2 as priority"));

        $avoirs = DB::table('favoirs')
            ->where('fournisseurid', $id)
            ->select('favoirdate as date', DB::raw("CAST(favoirdate AS DATE) as date_only"), DB::raw("'Avoir' as libelle"), DB::raw("CAST(favoirnumero AS TEXT) as numero"), 'totalttc as debit', DB::raw("0 as credit"), DB::raw("favoirid as doc_id"), DB::raw("'avoir' as type_slug"), DB::raw("2 as priority"));

        $bls = DB::table('fbls')
            ->where('fournisseurid', $id)
            ->where('transfere', false)
            ->select('fbldate as date', DB::raw("CAST(fbldate AS DATE) as date_only"), DB::raw("'Bon Entrée' as libelle"), DB::raw("CAST(fblnumero AS TEXT) as numero"), DB::raw("0 as debit"), 'totalttc as credit', DB::raw("fblid as doc_id"), DB::raw("'bon-entree' as type_slug"), DB::raw("1 as priority"));

        $brs = DB::table('fbrs')
            ->where('fournisseurid', $id)
            ->where('transfere', false)
            ->select('fbrdate as date', DB::raw("CAST(fbrdate AS DATE) as date_only"), DB::raw("'Bon Sortie' as libelle"), DB::raw("CAST(fbrnumero AS TEXT) as numero"), 'totalttc as debit', DB::raw("0 as credit"), DB::raw("fbrid as doc_id"), DB::raw("'bon-sortie' as type_slug"), DB::raw("2 as priority"));

        $movements = $factures->union($reglements)->union($avoirs)->union($bls)->union($brs)
            ->orderBy('date_only')
            ->orderBy('priority')
            ->orderBy('date')
            ->get();

        // Pre-load details for Bons, Factures, Avoirs, Reglements
        $blIds = $movements->where('type_slug', 'bon-entree')->pluck('doc_id')->toArray();
        $brIds = $movements->where('type_slug', 'bon-sortie')->pluck('doc_id')->toArray();
        $factureIds = $movements->where('type_slug', 'facture')->pluck('doc_id')->toArray();
        $avoirIds = $movements->where('type_slug', 'avoir')->pluck('doc_id')->toArray();
        $regIds = $movements->where('type_slug', 'reglement')->pluck('doc_id')->toArray();

        $blHeaders = DB::table('fbls')
            ->join('fournisseurs', 'fbls.fournisseurid', '=', 'fournisseurs.fournisseurid')
            ->whereIn('fblid', $blIds)
            ->select('fbls.*', 'fournisseurs.nom as fournisseur_nom')
            ->get()->keyBy('fblid');

        $brHeaders = DB::table('fbrs')
            ->join('fournisseurs', 'fbrs.fournisseurid', '=', 'fournisseurs.fournisseurid')
            ->whereIn('fbrid', $brIds)
            ->select('fbrs.*', 'fournisseurs.nom as fournisseur_nom')
            ->get()->keyBy('fbrid');

        $factureHeaders = DB::table('ffactures')->whereIn('ffactureid', $factureIds)->get()->keyBy('ffactureid');
        $avoirHeaders = DB::table('favoirs')->whereIn('favoirid', $avoirIds)->get()->keyBy('favoirid');

        $regDetails = DB::table('freglements')
            ->leftJoin('modereglements', 'freglements.modereglementid', '=', 'modereglements.modereglementid')
            ->whereIn('freglementid', $regIds)
            ->select('freglements.*', 'modereglements.libelle as mode_libelle')
            ->get()->keyBy('freglementid');

        $blDetails = DB::table('detfbls')
            ->join('produits', 'detfbls.produitid', '=', 'produits.produitid')
            ->whereIn('fblid', $blIds)
            ->select('detfbls.*', 'produits.produitlibelle', 'produits.produitcode')
            ->get()->groupBy('fblid');

        $brDetails = DB::table('detfbrs')
            ->join('produits', 'detfbrs.produitid', '=', 'produits.produitid')
            ->whereIn('fbrid', $brIds)
            ->select('detfbrs.*', 'produits.produitlibelle', 'produits.produitcode')
            ->get()->groupBy('fbrid');

        $factureDetails = DB::table('detffactures')
            ->join('produits', 'detffactures.produitid', '=', 'produits.produitid')
            ->whereIn('ffactureid', $factureIds)
            ->select('detffactures.*', 'produits.produitlibelle', 'produits.produitcode')
            ->get()->groupBy('ffactureid');

        $avoirDetails = DB::table('detfavoirs')
            ->join('produits', 'detfavoirs.produitid', '=', 'produits.produitid')
            ->whereIn('favoirid', $avoirIds)
            ->select('detfavoirs.*', 'produits.produitlibelle', 'produits.produitcode')
            ->get()->groupBy('favoirid');

        foreach ($movements as $m) {
            if ($m->type_slug == 'bon-entree') {
                $m->header = $blHeaders->get($m->doc_id);
                $m->details = $blDetails->get($m->doc_id);
            } elseif ($m->type_slug == 'bon-sortie') {
                $m->header = $brHeaders->get($m->doc_id);
                $m->details = $brDetails->get($m->doc_id);
            } elseif ($m->type_slug == 'facture') {
                $m->header = $factureHeaders->get($m->doc_id);
                $m->details = $factureDetails->get($m->doc_id);
            } elseif ($m->type_slug == 'avoir') {
                $m->header = $avoirHeaders->get($m->doc_id);
                $m->details = $avoirDetails->get($m->doc_id);
            } elseif ($m->type_slug == 'reglement') {
                $m->extra = $regDetails->get($m->doc_id);
            }
        }

        $facturesTotal = DB::table('ffactures')->where('fournisseurid', $id)->sum('totalttc');
        $reglementsTotal = DB::table('freglements')->where('fournisseurid', $id)->sum('montant');
        $avoirsTotal = DB::table('favoirs')->where('fournisseurid', $id)->sum('totalttc');
        $bonsEntreeTotal = DB::table('fbls')->where('fournisseurid', $id)->where('transfere', false)->sum('totalttc');
        $bonsSortieTotal = DB::table('fbrs')->where('fournisseurid', $id)->where('transfere', false)->sum('totalttc');
        $soldeDepart = $fournisseur->soldeinitial ?? 0;

        $soldeFinal = $soldeDepart + $facturesTotal + $bonsEntreeTotal - $reglementsTotal - $avoirsTotal - $bonsSortieTotal;

        $summary = [
            'solde_depart' => $soldeDepart,
            'factures' => $facturesTotal,
            'reglements' => $reglementsTotal,
            'avoirs' => $avoirsTotal,
            'bons_entree' => $bonsEntreeTotal,
            'bons_sortie' => $bonsSortieTotal,
            'solde_final' => $soldeFinal
        ];

        return Inertia::render('Statement', [
            'fournisseur' => $fournisseur,
            'movements' => $movements,
            'summary' => $summary
        ]);
    });

    Route::get('/achats', function () {
        $factures = DB::table('ffactures')
            ->join('fournisseurs', 'ffactures.fournisseurid', '=', 'fournisseurs.fournisseurid')
            ->select(
                'ffactureid as id',
                'ffactures.fournisseurid',
                'fournisseurs.nom as fournisseur_nom',
                'ffacturedate as date',
                'ffacturenumero as numero',
                'totalttc',
                DB::raw("'Facture' as type_libelle"),
                DB::raw("'facture' as type_slug")
            );

        $bls = DB::table('fbls')
            ->join('fournisseurs', 'fbls.fournisseurid', '=', 'fournisseurs.fournisseurid')
            ->where('transfere', false)
            ->select(
                'fblid as id',
                'fbls.fournisseurid',
                'fournisseurs.nom as fournisseur_nom',
                'fbldate as date',
                'fblnumero as numero',
                'totalttc',
                DB::raw("'Bon Entrée' as type_libelle"),
                DB::raw("'bon-entree' as type_slug")
            );

        $purchases = $factures->union($bls)
            ->orderBy('date', 'desc')
            ->limit(100)
            ->get();

        return Inertia::render('Purchases', [
            'purchases' => $purchases
        ]);
    })->name('achats');

    Route::get('/bon-entree/{id}', function (Request $request, $id) {
        $bon = DB::table('fbls')
            ->join('fournisseurs', 'fbls.fournisseurid', '=', 'fournisseurs.fournisseurid')
            ->where('fblid', $id)
            ->select('fbls.*', 'fournisseurs.nom as fournisseur_nom')
            ->first();

        if (!$bon)
            abort(404);

        $details = DB::table('detfbls')
            ->join('produits', 'detfbls.produitid', '=', 'produits.produitid')
            ->where('fblid', $id)
            ->select('detfbls.*', 'produits.produitlibelle', 'produits.produitcode')
            ->get();

        if ($request->ajax() || $request->has('partial')) {
            return view('partials.details_bon_content', ['type' => 'Bon Entrée', 'bon' => $bon, 'details' => $details, 'numero' => $bon->fblnumero]);
        }

        return view('details_bon', ['type' => 'Bon Entrée', 'bon' => $bon, 'details' => $details, 'numero' => $bon->fblnumero]);
    });

    Route::get('/bon-sortie/{id}', function (Request $request, $id) {
        $bon = DB::table('fbrs')
            ->join('fournisseurs', 'fbrs.fournisseurid', '=', 'fournisseurs.fournisseurid')
            ->where('fbrid', $id)
            ->select('fbrs.*', 'fournisseurs.nom as fournisseur_nom')
            ->first();

        if (!$bon)
            abort(404);

        $details = DB::table('detfbrs')
            ->join('produits', 'detfbrs.produitid', '=', 'produits.produitid')
            ->where('fbrid', $id)
            ->select('detfbrs.*', 'produits.produitlibelle', 'produits.produitcode')
            ->get();

        if ($request->ajax() || $request->has('partial')) {
            return view('partials.details_bon_content', ['type' => 'Bon Sortie', 'bon' => $bon, 'details' => $details, 'numero' => $bon->fbrnumero]);
        }

        return view('details_bon', ['type' => 'Bon Sortie', 'bon' => $bon, 'details' => $details, 'numero' => $bon->fbrnumero]);
    });

    Route::get('/stock', [\App\Http\Controllers\StockController::class, 'index'])->name('stock.index');

    Route::get('/statistiques', [\App\Http\Controllers\StatisticsController::class, 'index'])->name('statistiques.index');

    Route::get('/clients', function () {
        $clients = DB::table('clients')
            ->leftJoin('clientfamilles', 'clients.clientfamilleid', '=', 'clientfamilles.clientfamilleid')
            ->select('clients.*', 'clientfamilles.libelle as famille')
            ->orderBy('clients.solde', 'desc')
            ->get();

        return Inertia::render('Clients', [
            'clients' => $clients
        ]);
    })->name('clients');

    Route::get('/client/{id}/releve', function ($id) {
        $client = DB::table('clients')
            ->leftJoin('clientfamilles', 'clients.clientfamilleid', '=', 'clientfamilles.clientfamilleid')
            ->select('clients.*', 'clientfamilles.libelle as famille')
            ->where('clients.clientid', $id)
            ->first();

        if (!$client)
            abort(404);

        $movements = DB::table('cfactures')->where('clientid', $id)
            ->select('cfacturedate as date', DB::raw("CAST(cfacturedate AS DATE) as date_only"), DB::raw("'Facture' as libelle"), DB::raw("CAST(cfacturenumero AS TEXT) as numero"), 'totalttc as debit', DB::raw("0 as credit"), DB::raw("cfactureid as doc_id"), DB::raw("'facture' as type_slug"), DB::raw("1 as priority"))
            ->union(DB::table('creglements')
            ->where('clientid', $id)
            ->select('date', DB::raw("CAST(date AS DATE) as date_only"), DB::raw("'Règlement' as libelle"), DB::raw("CAST(numero AS TEXT) as numero"), DB::raw("0 as debit"), 'montant as credit', DB::raw("creglementid as doc_id"), DB::raw("'reglement' as type_slug"), DB::raw("2 as priority")))
            ->union(DB::table('cavoirs')
            ->where('clientid', $id)
            ->select('cavoirdate as date', DB::raw("CAST(cavoirdate AS DATE) as date_only"), DB::raw("'Avoir' as libelle"), DB::raw("CAST(cavoirnumero AS TEXT) as numero"), DB::raw("0 as debit"), 'totalttc as credit', DB::raw("cavoirid as doc_id"), DB::raw("'avoir' as type_slug"), DB::raw("2 as priority")))
            ->union(DB::table('cbls')
            ->where('clientid', $id)
            ->where('transfere', false)
            ->select('cbldate as date', DB::raw("CAST(cbldate AS DATE) as date_only"), DB::raw("'Bon Livraison' as libelle"), DB::raw("CAST(cblnumero AS TEXT) as numero"), 'totalttc as debit', DB::raw("0 as credit"), DB::raw("cblid as doc_id"), DB::raw("'bon-sortie' as type_slug"), DB::raw("1 as priority")))
            ->union(DB::table('cbrs')
            ->where('clientid', $id)
            ->where('transfere', false)
            ->select('cbrdate as date', DB::raw("CAST(cbrdate AS DATE) as date_only"), DB::raw("'Bon Retour' as libelle"), DB::raw("CAST(cbrnumero AS TEXT) as numero"), DB::raw("0 as debit"), 'totalttc as credit', DB::raw("cbrid as doc_id"), DB::raw("'bon-entree' as type_slug"), DB::raw("2 as priority")))
            ->orderBy('date_only')
            ->orderBy('priority')
            ->orderBy('date')
            ->get();

        // Pre-load details for Bons, Factures, Avoirs, Reglements
        $blIds = $movements->where('type_slug', 'bon-sortie')->pluck('doc_id')->toArray();
        $brIds = $movements->where('type_slug', 'bon-entree')->pluck('doc_id')->toArray();
        $factureIds = $movements->where('type_slug', 'facture')->pluck('doc_id')->toArray();
        $avoirIds = $movements->where('type_slug', 'avoir')->pluck('doc_id')->toArray();
        $regIds = $movements->where('type_slug', 'reglement')->pluck('doc_id')->toArray();

        $blHeaders = DB::table('cbls')
            ->join('clients', 'cbls.clientid', '=', 'clients.clientid')
            ->whereIn('cblid', $blIds)
            ->select('cbls.*', 'clients.nom as client_nom')
            ->get()->keyBy('cblid');

        $brHeaders = DB::table('cbrs')
            ->join('clients', 'cbrs.clientid', '=', 'clients.clientid')
            ->whereIn('cbrid', $brIds)
            ->select('cbrs.*', 'clients.nom as client_nom')
            ->get()->keyBy('cbrid');

        $factureHeaders = DB::table('cfactures')->whereIn('cfactureid', $factureIds)->get()->keyBy('cfactureid');
        $avoirHeaders = DB::table('cavoirs')->whereIn('cavoirid', $avoirIds)->get()->keyBy('cavoirid');

        $regDetails = DB::table('creglements')
            ->leftJoin('modereglements', 'creglements.modereglementid', '=', 'modereglements.modereglementid')
            ->whereIn('creglementid', $regIds)
            ->select('creglements.*', 'modereglements.libelle as mode_libelle')
            ->get()->keyBy('creglementid');

        $blDetails = DB::table('detcbls')
            ->join('produits', 'detcbls.produitid', '=', 'produits.produitid')
            ->whereIn('cblid', $blIds)
            ->select('detcbls.*', 'produits.produitlibelle', 'produits.produitcode')
            ->get()->groupBy('cblid');

        $brDetails = DB::table('detcbrs')
            ->join('produits', 'detcbrs.produitid', '=', 'produits.produitid')
            ->whereIn('cbrid', $brIds)
            ->select('detcbrs.*', 'produits.produitlibelle', 'produits.produitcode')
            ->get()->groupBy('cbrid');

        $factureDetails = DB::table('detcfactures')
            ->join('produits', 'detcfactures.produitid', '=', 'produits.produitid')
            ->whereIn('cfactureid', $factureIds)
            ->select('detcfactures.*', 'produits.produitlibelle', 'produits.produitcode')
            ->get()->groupBy('cfactureid');

        $avoirDetails = DB::table('detcavoirs')
            ->join('produits', 'detcavoirs.produitid', '=', 'produits.produitid')
            ->whereIn('cavoirid', $avoirIds)
            ->select('detcavoirs.*', 'produits.produitlibelle', 'produits.produitcode')
            ->get()->groupBy('cavoirid');

        foreach ($movements as $m) {
            if ($m->type_slug == 'bon-sortie') {
                $m->header = $blHeaders->get($m->doc_id);
                $m->details = $blDetails->get($m->doc_id);
            } elseif ($m->type_slug == 'bon-entree') {
                $m->header = $brHeaders->get($m->doc_id);
                $m->details = $brDetails->get($m->doc_id);
            } elseif ($m->type_slug == 'facture') {
                $m->header = $factureHeaders->get($m->doc_id);
                $m->details = $factureDetails->get($m->doc_id);
            } elseif ($m->type_slug == 'avoir') {
                $m->header = $avoirHeaders->get($m->doc_id);
                $m->details = $avoirDetails->get($m->doc_id);
            } elseif ($m->type_slug == 'reglement') {
                $m->extra = $regDetails->get($m->doc_id);
            }
        }

        $facturesTotal = DB::table('cfactures')->where('clientid', $id)->sum('totalttc');
        $reglementsTotal = DB::table('creglements')->where('clientid', $id)->sum('montant');
        $avoirsTotal = DB::table('cavoirs')->where('clientid', $id)->sum('totalttc');
        $bonsSortieTotal = DB::table('cbls')->where('clientid', $id)->where('transfere', false)->sum('totalttc');
        $bonsEntreeTotal = DB::table('cbrs')->where('clientid', $id)->where('transfere', false)->sum('totalttc');
        
        $soldeDepart = $client->soldeinitial ?? 0;
        $soldeFinal = $soldeDepart + $facturesTotal + $bonsSortieTotal - $reglementsTotal - $avoirsTotal - $bonsEntreeTotal;

        $summary = [
            'solde_depart' => $soldeDepart,
            'factures' => $facturesTotal,
            'reglements' => $reglementsTotal,
            'avoirs' => $avoirsTotal,
            'bons_sortie' => $bonsSortieTotal,
            'bons_entree' => $bonsEntreeTotal,
            'solde_final' => $soldeFinal
        ];

        return Inertia::render('ClientStatement', [
            'client' => $client,
            'movements' => $movements,
            'summary' => $summary
        ]);
    });
});
