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

    }

    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\clientController::showCoordonneesClient')->bind('client.index');
        $index->put("/update", 'App\Controller\clientController::modifierCoordonnes')->bind('client.update');
        return $index;
    }

}