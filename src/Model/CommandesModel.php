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
            ->select('id','user_id','prix','date_achat','etat_id')
            ->from('commandes')
            ->where('user_id='.$user);

        return $queryBuilder->execute()->fetchAll();
    }
}