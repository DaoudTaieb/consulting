<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Site;
use App\Models\Famille;
use App\Models\Sousfamille;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Stock::select('stockid', 'produitid', 'siteid', 'qtestock', 'stockvirtuel', 'valeurstockttc')
            ->with([
                'produit' => function ($q) {
                    $q->select('produitid', 'produitlibelle', 'produitcode', 'familleid', 'sousfamilleid', 'fournisseurid', 'pmarque', 'ht_vente', 'ttc_vente');
                },
                'produit.famille:familleid,famillelibelle',
                'produit.sousfamille:sousfamilleid,sousfamillelibelle',
                'produit.fournisseur:fournisseurid,nom',
                'site' => function ($q) {
                    $q->select('siteid', 'libelle as sitelibelle');
                }
            ]);

        // Filtering
        if ($request->filled('siteid')) {
            $query->where('siteid', $request->siteid);
        }

        if ($request->filled('familleid')) {
            $query->whereHas('produit', function ($q) use ($request) {
                $q->where('familleid', $request->familleid);
            });
        }

        if ($request->filled('sousfamilleid')) {
            $query->whereHas('produit', function ($q) use ($request) {
                $q->where('sousfamilleid', $request->sousfamilleid);
            });
        }

        if ($request->filled('fournisseurid')) {
            $query->whereHas('produit', function ($q) use ($request) {
                $q->where('fournisseurid', $request->fournisseurid);
            });
        }

        if ($request->filled('marque')) {
            $query->whereHas('produit', function ($q) use ($request) {
                $q->where('pmarque', 'LIKE', '%' . $request->marque . '%');
            });
        }

        if ($request->filled('search')) {
            $query->whereHas('produit', function ($q) use ($request) {
                $q->where('produitlibelle', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('produitcode', 'LIKE', '%' . $request->search . '%');
            });
        }

        $stocks = $query->get();

        foreach ($stocks as $stock) {
            // Prix unitaire TTC : colonne ttc_vente de la table produits (pas prixventeht)
            $stock->prixunitairettc = round((float) ($stock->produit->ttc_vente ?? 0), 3);
            // Quantité affichée : qtestock ou stockvirtuel en secours
            $qte = (float) ($stock->qtestock ?: $stock->stockvirtuel ?? 0);
            $stock->qte_affichee = $qte;
            // Valeur stock TTC : colonne en base si > 0, sinon qte × prix unitaire TTC
            $valeurDb = (float) ($stock->valeurstockttc ?? 0);
            if ($valeurDb > 0) {
                $stock->valeurstockttc = round($valeurDb, 3);
            } else {
                $stock->valeurstockttc = round($qte * $stock->prixunitairettc, 3);
            }
        }

        // Ne garder que les lignes qui ont des données dans le tableau (qte, prix ou valeur > 0)
        $stocks = $stocks->filter(function ($stock) {
            return ($stock->qte_affichee > 0) || ($stock->prixunitairettc > 0) || ($stock->valeurstockttc > 0);
        })->values();

        return Inertia::render('Stock', [
            'stocks' => $stocks,
            'sites' => Site::select('siteid', 'libelle as sitelibelle')->get(),
            'familles' => Famille::select('familleid', 'famillelibelle')->get(),
            'sousfamilles' => Sousfamille::select('sousfamilleid', 'sousfamillelibelle', 'familleid')->get(),
            'fournisseurs' => Fournisseur::select('fournisseurid', 'nom')->get(),
            'filters' => $request->only(['siteid', 'familleid', 'sousfamilleid', 'fournisseurid', 'marque', 'search']),
        ]);
    }
}
