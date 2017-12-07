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
            ->where('produit_id = '.$id)
            ->orderBy('c.id');
        return $queryBuilder->execute()->fetchAll();
    }

    public function insertCommentaires($produit_id,$user_id,$donnees){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->insert('commentaires')
            ->values([
                'commentaire' => '?',
                'produit_id' => '?',
                'user_id' => '?'
            ])

            ->setParameter(0, $donnees['commentaire'])
            ->setParameter(1, $produit_id)
            ->setParameter(2, $user_id);
        return $queryBuilder->execute();
    }

    public function getOneCommentairesOnOneProduit($id)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('c.commentaire')
            ->from('commentaires','c')
            ->where('id = '.$id);
        return $queryBuilder->execute()->fetch();
    }

    public function deleteCommentaire($id)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->delete('commentaires')
            ->where('id='.$id);

        return $queryBuilder->execute();
    }
}