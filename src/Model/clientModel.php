<?php
namespace App\Model;
use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class clientModel
{
    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }

    public function getCoordonneesClientById($user)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select ('*')
            ->from('users')
            ->where('id = '.$user);

        return $queryBuilder->execute()->fetch();
    }
    
    public function editClient($donnees) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('users')
            ->set('nom', '?')
            ->set('username','?')
            ->set('code_postal','?')
            ->set('adresse','?')
            ->set('ville','?')
            ->where('id= ?')
            ->setParameter(0, $donnees['nom'])
            ->setParameter(1, $donnees['username'])
            ->setParameter(2, $donnees['code_postal'])
            ->setParameter(3, $donnees['adresse'])
            ->setParameter(4, $donnees['ville'])
            ->setParameter(5, $donnees['id']);
        return $queryBuilder->execute();
    }
}
