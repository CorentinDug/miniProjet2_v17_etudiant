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
    private $userModel;

    public function index(Application $app){
        return $this->showAllCommentaires($app);
    }

    public function showCommentaires(Application $app,$id){
        $this->commentaireModel = new CommentaireModel($app);
        $this->userModel = new UserModel($app);
        $allCommentairesProduits = $this->commentaireModel->getAllCommentairesOnOneProduit($id);
        $donneesClient = $this->userModel->getUser($app['session']->get('user_id'));
        return $app['twig']->render('frontOff/commentaires/showAllCommentairesOnOneProduit.html.twig',['commentaires'=>$allCommentairesProduits,'donneesClient'=>$donneesClient]);
    }

    public function showAllCommentaires($app)
    {

    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->get('/', 'App\Controller\CommentaireController::index')->bind('commentaire.index');
        $controllers->get('/showClient/{id}', 'App\Controller\CommentaireController::showCommentaires')->bind('commentaire.showCommentairesClient');
        $controllers->get('/add', 'App\Controller\CommentaireController::addComentaires')->bind('commentaire.addCommentaires');
        $controllers->post('/add', 'App\Controller\CommentaireController::validFormAddCommentaires')->bind('commentaire.validFormAddCommentaires');
        return $controllers;
    }
}