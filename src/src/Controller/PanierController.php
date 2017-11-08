<?php
namespace App\Controller;

use App\Model\PanierModel;
use App\Model\ProduitModel;
use App\Model\TypeProduitModel;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class PanierController implements ControllerProviderInterface{

    private $produitModel;
    private $typeProduitModel;
    private $panierModel;

    public function index(Application $app){
        return $this->showPagePanierProduits($app);
    }

    public function showPagePanierProduits(Application $app){
        $this->produitModel = new ProduitModel($app);
        $produitModel = $this->produitModel->getAllProduits();

        $user = $app['session']->get('user_id');
        $this->panierModel = new PanierModel($app);
        $panierModel = $this->panierModel->getAllPanier($user);

        return $app["twig"]->render('frontOff/frontOFFICE.html.twig',['produits'=>$produitModel,'panier'=>$panierModel]);
    }

    public function addPanier(Application $app,$id){
        $user = $app['session']->get('user_id');
        $this->produitModel = new ProduitModel($app);
        $produitModel = $this->produitModel->getProduit($id);
        var_dump($produitModel);

        return "bonjour";
    }

    public function deletePanier(Application $app){

    }

    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\PanierController::showPagePanierProduits')->bind('Panier.index');
        $index->get("/add/{id}", 'App\Controller\PanierController::addPanier')->bind('Panier.add');
        $index->match("/delete", 'App\Controller\PanierController::deletePanier')->bind('Panier.delete');
        $index->match("/details", 'App\Controller\PanierController::detailsProduit')->bind('Panier.details');
        return $index;
    }
}