<?php

namespace App\Controller;
use App\Model\CommandesModel;
use App\Model\PanierModel;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class commandeController implements ControllerProviderInterface
{
    private $commandeClient;
    private $prixTotalCommande;
    private $panierAMettreAJour;

    public function index(Application $app){
        return $this->showCommandes($app);
    }

    private function showCommandes(Application $app)
    {
        $user = $app['session']->get('user_id');
        $this->commandeClient = new CommandesModel($app);
        $commande = $this->commandeClient->getCommandeByIDClient($user);

        return $app["twig"]->render('frontOff/commande/showCommandes.html.twig',['donneesCommande'=>$commande]);
    }

    public function validerCommande(Application $app){
        $user = $app['session']->get('user_id');
        $this->commandeClient = new CommandesModel($app);
        $this->prixTotalCommande = new PanierModel($app);
        $this->panierAMettreAJour = new PanierModel($app);

        $prixTotal = $this->prixTotalCommande->getPrixByPanier($user);
        $prix = $prixTotal['prixTotal'];

        $this->commandeClient->addCommandesByClient($user,$prix);
        $commandeID = $this->commandeClient->getIDCommande($user,$prix);
        $commandeId = $commandeID['id'];
        $this->panierAMettreAJour->miseAJourPanierApresCommande($user,$commandeId);

        return $app->redirect($app["url_generator"]->generate("Panier.index"));
    }

    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\commandeController::index')->bind('commande.index');
        $index->match("/addCommande", 'App\Controller\commandeController::validerCommande')->bind('commande.valider');
        return $index;
    }
}