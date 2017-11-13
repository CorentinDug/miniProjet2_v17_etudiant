<?php
namespace App\Controller;

use App\Model\CommandesModel;
use App\Model\PanierModel;
use App\Model\ProduitModel;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class PanierController implements ControllerProviderInterface{

    private $produitModel;
    private $typeProduitModel;
    private $panierModel;
    private $commandesModel;
    private $panierQuantite;

    public function index(Application $app){
        return $this->showPagePanierProduits($app);
    }

    public function showPagePanierProduits(Application $app){
        $this->produitModel = new ProduitModel($app);
        $produitModel = $this->produitModel->getAllProduits();

        $user = $app['session']->get('user_id');
        $this->panierModel = new PanierModel($app);
        $panierModel = $this->panierModel->getAllPanier($user);

        $quantiteEtPrix = $this->panierModel->getQuantiteEtPrix($user);

        return $app["twig"]->render('frontOff/frontOFFICE.html.twig',['produits'=>$produitModel,'panier'=>$panierModel]);
    }

    public function addPanier(Application $app,$id){
        $user = $app['session']->get('user_id');

        $this->produitModel = new ProduitModel($app);
        $produitModel = $this->produitModel->getProduit($id);

        $this->commandesModel = new CommandesModel($app);
        $commandesModel = $this->commandesModel->getNombreCommandes();

        $this->panierQuantite = new PanierModel($app);
        $panierQuantite = $this->panierQuantite->getQuantiteById($id,$user);

        $this->panierModel = new PanierModel($app);

        if ($panierQuantite['quantite'] == null){
            $panierQuantite['quantite'] = 1;
            $panierModel = $this->panierModel->ajouterAuPanier($user,$produitModel,$commandesModel,$panierQuantite);
        }else{
            $panierQuantite['quantite'] += 1;
            $panierModel = $this->panierModel->modifierQuantitePanier($id,$panierQuantite,$user);
        }

        return $app->redirect($app["url_generator"]->generate("Panier.index"));
    }

    public function deletePanier(Application $app,$id){
        $user = $app['session']->get('user_id');

        $this->panierModel = new PanierModel($app);
        $panierModel = $this->panierModel->getProduitDansPanierById($id);

        if ($panierModel['quantite'] == 1){
            var_dump($panierModel['quantite']);
            $panierModel = $this->panierModel->supprimerProduitDuPanier($id);
        }else{
            $panierModel['quantite'] -= 1;
            $panierModel = $this->panierModel->modifierQuantitePanier($panierModel['produit_id'],$panierModel,$user);
        }

        return $app->redirect($app["url_generator"]->generate("Panier.index"));
    }

    public function detailsProduit(Application $app,$id){
        $this->produitModel = new ProduitModel($app);
        $produitModel = $this->produitModel->getProduit($id);

        return $app["twig"]->render("produit/detailsProduit.html.twig",['produits'=>$produitModel]);
    }

    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\PanierController::showPagePanierProduits')->bind('Panier.index');
        $index->get("/add/{id}", 'App\Controller\PanierController::addPanier')->bind('Panier.add');
        $index->match("/delete/{id}", 'App\Controller\PanierController::deletePanier')->bind('Panier.delete');
        $index->match("/details/{id}", 'App\Controller\PanierController::detailsProduit')->bind('Panier.details');
        return $index;
    }
}