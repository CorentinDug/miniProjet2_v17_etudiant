<?php
namespace App\Controller;
use App\Model\clientModel;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class clientController implements ControllerProviderInterface
{
    private $clientModel;

    public function index(Application $app){
        return $this->showCoordonneesClient($app);
    }

    public function showCoordonneesClient(Application $app)
    {
        $user = $app['session']->get('user_id');
        $this->clientModel = new clientModel($app);
        $clientModel = $this->clientModel->getCoordonneesClientById($user);

        return $app["twig"]->render('frontOff/client/showCoordonnees.html.twig',['donnees'=>$clientModel]);
    }

    public function modifierCoordonnees(Application $app,$id){
        $this->clientModel = new ClientModel($app);
        $donnees = $this->clientModel->getCoordonneesClientById($id);
        return $app["twig"]->render('frontOff/client/editClientCoordonnees.html.twig',['donnees'=>$donnees]);
    }

    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\clientController::showCoordonneesClient')->bind('client.index');
        $index->get("/update/{id}", 'App\Controller\clientController::modifierCoordonnees')->bind('client.update');
        return $index;
    }

}