<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$id = 4133278; // code from image? The image has code 4133278, let's just query a client
$client = DB::table('clients')->first();
$id = 4133278;

try {
    $movements = DB::table('cfactures')->where('clientid', $id)
            ->select('cfacturedate as date', DB::raw("'Facture' as libelle"), DB::raw("CAST(cfacturenumero AS TEXT) as numero"), 'totalttc as debit', DB::raw("0 as credit"), DB::raw("cfactureid as doc_id"), DB::raw("'facture' as type_slug"))
            ->union(DB::table('creglements')
            ->where('clientid', $id)
            ->select('date', DB::raw("'Règlement' as libelle"), DB::raw("CAST(numero AS TEXT) as numero"), DB::raw("0 as debit"), 'montant as credit', DB::raw("creglementid as doc_id"), DB::raw("'reglement' as type_slug")))
            ->union(DB::table('cavoirs')
            ->where('clientid', $id)
            ->select('cavoirdate as date', DB::raw("'Avoir' as libelle"), DB::raw("CAST(cavoirnumero AS TEXT) as numero"), DB::raw("0 as debit"), 'totalttc as credit', DB::raw("cavoirid as doc_id"), DB::raw("'avoir' as type_slug")))
            ->union(DB::table('cbls')
            ->where('clientid', $id)
            ->where('transfere', false)
            ->select('cbldate as date', DB::raw("'Bon Livraison' as libelle"), DB::raw("CAST(cblnumero AS TEXT) as numero"), 'totalttc as debit', DB::raw("0 as credit"), DB::raw("cblid as doc_id"), DB::raw("'bon-sortie' as type_slug")))
            ->union(DB::table('cbrs')
            ->where('clientid', $id)
            ->where('transfere', false)
            ->select('cbrdate as date', DB::raw("'Bon Retour' as libelle"), DB::raw("CAST(cbrnumero AS TEXT) as numero"), DB::raw("0 as debit"), 'totalttc as credit', DB::raw("cbrid as doc_id"), DB::raw("'bon-entree' as type_slug")))
            ->orderBy('date')
            ->get();

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
    echo "Success! " . count($movements) . " movements.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getFile() . ":" . $e->getLine() . "\n";
}
