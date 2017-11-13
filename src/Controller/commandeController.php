<?php

namespace App\Controller;
use App\Model\CommandesModel;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class commandeController implements ControllerProviderInterface
{
    private $commandeClient;

    public function index(Application $app){
        return $this->showCommandes($app);
    }

    private function showCommandes($app)
    {
        $user = $app['session']->get('user_id');
        $this->commandeClient = new CommandesModel($app);
        $commande = $this->commandeClient->getCommandeByIDClient($user);

        return $app["twig"]->render('frontOff/commande/showCommandes.html.twig',['donneesCommande'=>$commande]);
    }

    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\commandeController::index')->bind('commande.index');
        return $index;
    }
}