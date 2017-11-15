<?php
namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class PanierModel{
    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }

    public function getAllPanier($user){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('panier.id','panier.quantite','panier.prix','panier.dateAjoutPanier','panier.produit_id','panier.user_id')
            ->from('paniers', 'panier')
            ->where('panier.user_id = '.$user." and commande_id is NULL");

         return $queryBuilder->execute()->fetchAll();
    }

    public function getQuantiteEtPrix($user){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('quantite','prix')
            ->from('paniers')
            ->where('user_id='.$user);

        return $queryBuilder->execute()->fetchAll();
    }

    public function getQuantiteById($id,$user){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('quantite')
            ->from('paniers')
            ->where('produit_id='.$id.' and user_id='.$user.' and commande_id is null');

        return $queryBuilder->execute()->fetch();
    }

    public function ajouterAuPanier($user,$produitModel,$panierQuantite){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->insert('paniers')
            ->values([
                    'quantite' => '?',
                    'prix' => '?',
                    'dateAjoutPanier' => 'CURDATE()',
                    'user_id' => '?',
                    'produit_id' => '?',
                    'commande_id' => 'NULL'
            ])
            ->setParameter(0,$panierQuantite['quantite'])
            ->setParameter(1,$produitModel['prix'])
            ->setParameter(2,$user)
            ->setParameter(3,$produitModel['id'])
        ;
        return $queryBuilder->execute();
    }

    public function modifierQuantitePanier($id,$panierQuantite,$user)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('paniers')
            ->set('quantite','?')
            ->where('produit_id='.$id.' and user_id='.$user.' and commande_id is null')
            ->setParameter(0,$panierQuantite['quantite']);

        return $queryBuilder->execute();
    }

    public function getProduitDansPanierById($id)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('*')
            ->from('paniers')
            ->where('id='.$id);

        return $queryBuilder->execute()->fetch();
    }

    public function supprimerProduitDuPanier($id)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->delete('paniers')
            ->where('id='.$id);

        return $queryBuilder->execute();
    }

    public function getPrixByPanier($user){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('SUM(prix * quantite) as prixTotal')
            ->from('paniers')
            ->where('user_id='.$user." and commande_id is null");

        return $queryBuilder->execute()->fetch();
    }

    public function miseAJourPanierApresCommande($user,$commande){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('paniers')
            ->set('commande_id','?')
            ->where('user_id='.$user.' and commande_id is null')
            ->setParameter(0,$commande);

        return $queryBuilder->execute();
    }

    public function getCommandeByID($user,$id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('produit.nom','panier.quantite','panier.prix')
            ->from('paniers', 'panier')
            ->innerJoin('panier','produits','produit','panier.produit_id=produit.id')
            ->where('user_id='.$user." and commande_id=".$id);

        return $queryBuilder->execute()->fetchAll();
    }
}