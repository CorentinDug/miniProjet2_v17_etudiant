<?php
namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class CommandesModel
{
    private $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getNombreCommandes(){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('count(id)')
            ->from('commandes');

        return $queryBuilder->execute()->fetch();
    }

    public function getCommandeByIDClient($user)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('c.id','c.user_id','c.prix','c.date_achat','e.libelle')
            ->from('etats', 'e')
            ->innerJoin('e','commandes','c','c.etat_id=e.id')
            ->where('user_id='.$user);

        return $queryBuilder->execute()->fetchAll();
    }

    public function addCommandesByClient($user,$prix){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->insert('commandes')
            ->values([
                'user_id'=>'?',
                'prix'=>'?',
                'date_achat'=>'CURDATE()',
                'etat_id'=>'1'
            ])
            ->setParameter(0,$user)
            ->setParameter(1,$prix);

        return $queryBuilder->execute();
    }

    public function getIDCommande($user,$prix){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('id')
            ->from('commandes')
            ->where('user_id='.$user.' and prix='.$prix.' and date_achat = (select MAX(date_achat) from commandes)');

        return $queryBuilder->execute()->fetch();
    }
}