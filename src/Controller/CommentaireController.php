<?php
namespace App\Controller;

use App\Model\CommentaireModel;
use App\Model\UserModel;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

class CommentaireController implements ControllerProviderInterface{

    private $commentaireModel;

    public function showCommentaires(Application $app,$id){
        $this->commentaireModel = new CommentaireModel($app);
        $allCommentairesProduits = $this->commentaireModel->getAllCommentairesOnOneProduit($id);
        return $app['twig']->render('frontOff/commentaires/showAllCommentairesOnOneProduit.html.twig',['commentaires'=>$allCommentairesProduits]);
    }

    public function showAllCommentaires(Application $app,$id)
    {
        $this->commentaireModel = new CommentaireModel($app);
        $allCommentairesProduits = $this->commentaireModel->getAllCommentairesOnOneProduit($id);
        return $app['twig']->render('backOff/commentaires/showAllCommentaires.html.twig',['commentaires'=>$allCommentairesProduits]);
    }

    public function addComentaires(Application $app,$id){
        return $app["twig"]->render('frontOff/commentaires/ajouterCommentaires.html.twig',['id'=>$id]);
    }

    public function validFormAddCommentaires(Application $app,Request $req){
        $id=$app->escape($req->get('id'));
        $donnees = [
            'commentaire' => htmlspecialchars($_POST['commentaire'])
            ];

        if (strlen($donnees['commentaire']) < 5) $erreurs['commentaire'] = "La taille du commentiare doit être supérieure à 5";

        if (!empty($erreurs)){
            return $app["twig"]->render('frontOff/commentaires/ajouterCommentaires.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs,'id'=>$id]);
        }else{
            $this->commentaireModel = new CommentaireModel($app);
            $this->commentaireModel->insertCommentaires($id,$app['session']->get('user_id'),$donnees);
            return $app->redirect($app["url_generator"]->generate("produitClient.show"));
        }
    }

    public function deleteComentaires(Application $app,$id){
        $this->commentaireModel = new CommentaireModel($app);
        $commentaire = $this->commentaireModel->getOneCommentairesOnOneProduit($id);
        return $app["twig"]->render('backOff/commentaires/deleteCommentaires.html.twig',['id'=>$id,'com'=>$commentaire]);
    }

    public function validFormDeleteCom(Application $app,Request $req){
        $id=$app->escape($req->get('id'));
        if (is_numeric($id)) {
            $this->commentaireModel = new CommentaireModel($app);
            $this->commentaireModel->deleteCommentaire($id);
            return $app->redirect($app["url_generator"]->generate("produit.showProduits"));
        }
        else
            return $app->abort(404, 'error Pb id form Delete');
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->get('/showClient/{id}', 'App\Controller\CommentaireController::showCommentaires')->bind('commentaire.showCommentairesClient');
        $controllers->get('/show/{id}', 'App\Controller\CommentaireController::showAllCommentaires')->bind('commentaire.showCommentaire');
        $controllers->get('/add/{id}', 'App\Controller\CommentaireController::addComentaires')->bind('commentaire.add');
        $controllers->post('/add', 'App\Controller\CommentaireController::validFormAddCommentaires')->bind('commentaires.validFormAddCommentaire');
        $controllers->get('/delete/{id}', 'App\Controller\CommentaireController::deleteComentaires')->bind('commentaires.delete');
        $controllers->delete('/delete', 'App\Controller\CommentaireController::validFormDeleteCom')->bind('commentaires.validFormDeleteCommentaire');
        return $controllers;
    }
}