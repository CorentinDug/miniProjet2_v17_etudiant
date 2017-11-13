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
}