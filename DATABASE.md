# Base de données – Meuble Driss

## Connexion

| Paramètre   | Valeur        |
|------------|----------------|
| SGBD       | PostgreSQL     |
| Base       | `meubleidriss` |
| Hôte       | 127.0.0.1:5432 |
| Nombre de tables (schéma public) | 189 |

---

## Tables principales utilisées par l’application

### Utilisateurs
- **users** – Utilisateurs (userid, login, password, userdroitid, societeid, siteid, …)

### Fournisseurs
- **fournisseurs** – Comptes fournisseurs (fournisseurid, nom, code, solde, soldeinitial, fournisseurfamilleid, adresses, tel, email, …)
- **fournisseurfamilles** – Familles de fournisseurs (fournisseurfamilleid, fournisseurfamillelibelle)

### Achats / Mouvements fournisseurs
- **ffactures** – Factures fournisseurs (ffactureid, fournisseurid, ffacturenumero, ffacturedate, netapayer, totalttc, …)
- **freglements** – Règlements fournisseurs (freglementid, fournisseurid, date, montant, numero, …)
- **favoirs** – Avoirs fournisseurs (favoirid, fournisseurid, favoirnumero, favoirdate, netapayer, …)
- **fbls** – Bons de livraison / entrée (fblid, fournisseurid, fblnumero, fbldate, netapayer, transfere, …)
- **fbrs** – Bons de retour / sortie (fbrid, fournisseurid, fbrnumero, fbrdate, netapayer, transfere, …)
- **detfbls** – Détail des bons entrée (detfblid, fblid, produitid, qte, ht, ttc, totalttc, …)
- **detfbrs** – Détail des bons sortie (detfbrid, fbrid, produitid, qte, ht, ttc, …)

### Produits et stock
- **produits** – Catalogue (produitid, produitcode, produitlibelle, familleid, sousfamilleid, fournisseurid, pmarque, ht_vente, ttc_vente, …)
- **stocks** – Stock par produit et site (stockid, produitid, siteid, qtestock, stockvirtuel, valeurstockttc, …)
- **familles** – Familles de produits (familleid, famillelibelle)
- **sousfamilles** – Sous-familles (sousfamilleid, sousfamillelibelle, familleid)
- **sites** – Sites / dépôts (siteid, libelle, siteabrege)

### Caisse / Statistiques
- **journalcaisses** – Sessions caisse (journalcaisseid, caisseid, siteid, dateouverture, datecloture, fondcaisse, montantcloture, montanttheorique, isclosed, …)
- **journalcaissedets** – Lignes du journal caisse (journalcaisseid, sectionclotureid, libelle, valeur, priorite, …)
- **caisses** – Caisses (caisseid, libelle, siteid, …)
- **sectionclotures** – Sections de clôture (sectionclotureid, libelle, priorite)

---

## Schéma relationnel simplifié

```
fournisseurfamilles ──< fournisseurs ──┬──< ffactures
                                       ├──< freglements
                                       ├──< favoirs
                                       ├──< fbls ──< detfbls ──> produits
                                       └──< fbrs ──< detfbrs ──> produits

familles ──< sousfamilles ──< produits ──< stocks ──> sites

caisses ──< journalcaisses ──< journalcaissedets ──> sectionclotures
```

---

## Voir la base en détail

En console, à la racine du projet :

```bash
php show_db.php
```

Cela affiche la liste des tables principales avec toutes leurs colonnes et types.
