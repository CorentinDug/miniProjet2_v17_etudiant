<?php
namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class CommentaireModel{
    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }

    public function getAllCommentairesOnOneProduit($id)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('c.id','c.commentaire','u.username')
            ->from('commentaires','c')
            ->innerJoin('c','users','u','c.user_id=u.id ')
            ->where('produit_id = '.$id);
        return $queryBuilder->execute()->fetchAll();
    }
}