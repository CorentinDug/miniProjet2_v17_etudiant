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
            ->select('panier.id','panier.quantite','panier.prix','panier.dateAjoutPanier')
            ->from('paniers', 'panier')
            ->where('panier.user_id = '.$user);

         return $queryBuilder->execute()->fetchAll();
    }

    public function ajouterAuPanier($user){

    }
}