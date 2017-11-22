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
    private $panier;

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

        $this->commandeClient->addCommandesByClientWithTransaction($user,$prix);
        $commandeID = $this->commandeClient->getIDCommande($user,$prix);
        $commandeId = $commandeID['id'];
        $this->panierAMettreAJour->miseAJourPanierApresCommande($user,$commandeId);

        return $app->redirect($app["url_generator"]->generate("commande.index"));
    }

    public function voirAllCommandesClient(Application $app){
        $this->commandeClient = new CommandesModel($app);

        $donneesCommandes = $this->commandeClient->getAllCommandes();

        return $app['twig']->render('backOff/Commandes/showCommandes.html.twig',['donneesCommande'=>$donneesCommandes]);
    }

    public function modifierEtatCommande(Application $app,$id){
        $this->commandeClient = new CommandesModel($app);
        $etat_id = $this->commandeClient->getEtatID($id);
        var_dump($etat_id);
        $etat_id['etat_id'] +=1;
        if ($etat_id['etat_id'] < 4){
            $this->commandeClient->modifierEtatCommandes($id,$etat_id);
        }
        return $app->redirect($app['url_generator']->generate("commande.showAllCommandes"));
    }

    public function voirCommandeClient(Application $app,$id){
        $this->panier = new PanierModel($app);
        $produits = $this->panier->getCommandeByCID($id);

        return $app['twig']->render('backOff/Commandes/voirDetailsCommande.html.twig',['produits'=>$produits]);
    }

    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\commandeController::index')->bind('commande.index');
        $index->match("/addCommande", 'App\Controller\commandeController::validerCommande')->bind('commande.valider');
        $index->match("/showAllCommandes", 'App\Controller\commandeController::voirAllCommandesClient')->bind('commande.showAllCommandes');
        $index->match("/modifierEtatCommandes/{id}", 'App\Controller\commandeController::modifierEtatCommande')->bind('commande.modifierEtat');
        $index->match("/voirCommande/{id}", 'App\Controller\commandeController::voirCommandeClient')->bind('commande.voirCommande');

        return $index;
    }
}