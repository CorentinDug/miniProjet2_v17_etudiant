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
            ->where('panier.user_id = '.$user);

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
            ->where('produit_id='.$id.' and user_id='.$user);

        return $queryBuilder->execute()->fetch();
    }

    public function ajouterAuPanier($user,$produitModel,$commandeModel,$panierQuantite){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->insert('paniers')
            ->values([
                    'quantite' => '?',
                    'prix' => '?',
                    'dateAjoutPanier' => 'CURDATE()',
                    'user_id' => '?',
                    'produit_id' => '?',
                    'commande_id' => '?'
            ])
            ->setParameter(0,$panierQuantite['quantite'])
            ->setParameter(1,$produitModel['prix'])
            ->setParameter(2,$user)
            ->setParameter(3,$produitModel['id'])
            ->setParameter(4,$commandeModel['count(id)'])
        ;
        return $queryBuilder->execute();
    }

    public function modifierQuantitePanier($id,$panierQuantite,$user)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('paniers')
            ->set('quantite','?')
            ->where('produit_id='.$id.' and user_id='.$user)
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
}