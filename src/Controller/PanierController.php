<?php
namespace App\Controller;

use App\Model\CommandesModel;
use App\Model\PanierModel;
use App\Model\ProduitModel;
use App\Model\TypeProduitModel;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class PanierController implements ControllerProviderInterface{

    private $produitModel;
    private $panierModel;
    private $panierQuantite;
    private $typeProduitModel;
    private $commandesModel;

    public function index(Application $app){
        return $this->showPagePanierProduits($app);
    }

    public function showPagePanierProduits(Application $app){
        $this->produitModel = new ProduitModel($app);
        $donneeProduit = $this->produitModel->getAllProduits();

        $user = $app['session']->get('user_id');
        $this->panierModel = new PanierModel($app);
        $panierModel = $this->panierModel->getAllPanier($user);

        $prixTotal = $this->panierModel->getPrixByPanier($user);

        $this->typeProduitModel = new TypeProduitModel($app);
        $typeProduit = $this->typeProduitModel->getAllTypeProduits();
        if (isset($_POST['typeProduit_id'])){
            $donnee = [
                'typeProduit_id' =>htmlspecialchars($_POST['typeProduit_id'])
            ];
        }else{
            return $app['twig']->render('frontOff/frontOFFICE.html.twig',['donneesProduit'=>$donneeProduit,'typeProduits'=> $typeProduit,'prix'=>$prixTotal,'panier'=>$panierModel]);
        }

        if ($donnee['typeProduit_id'] == 0){
            $this->produitModel = new ProduitModel($app);
            $donneeProduit = $this->produitModel->getAllProduits();
            return $app['twig']->render('frontOff/frontOFFICE.html.twig',['donnees'=>$donnee,'donneesProduit' => $donneeProduit,'typeProduits'=> $typeProduit,'prix'=>$prixTotal,'panier'=>$panierModel]);
        }else {
            $this->produitModel = new ProduitModel($app);
            $donneeProduit = $this->produitModel->getProduitByProduitID($donnee['typeProduit_id']);
            return $app['twig']->render('frontOff/frontOFFICE.html.twig',['donnees'=>$donnee,'donneesProduit' => $donneeProduit,'typeProduits'=> $typeProduit,'prix'=>$prixTotal,'panier'=>$panierModel]);
        }

        //return $app["twig"]->render('frontOff/frontOFFICE.html.twig',['produits'=>$produitModel,'panier'=>$panierModel,'prix'=>$prixTotal]);
    }

    public function addPanier(Application $app,$id){
        $user = $app['session']->get('user_id');

        $this->produitModel = new ProduitModel($app);
        $produitModel = $this->produitModel->getProduit($id);

        $this->panierQuantite = new PanierModel($app);
        $panierQuantite = $this->panierQuantite->getQuantiteById($id,$user);

        $this->panierModel = new PanierModel($app);
        $stock = $this->produitModel->getStockByID($id);

            if ($panierQuantite['quantite'] == null){
                $panierQuantite['quantite'] = 1;
                $this->panierModel->ajouterAuPanier($user,$produitModel,$panierQuantite);
            }else{
                $panierQuantite['quantite'] += 1;
                $this->panierModel->modifierQuantitePanier($id,$panierQuantite,$user);
            }
            $stock['stock'] -= 1;
            $this->produitModel->updateStock($id,$stock['stock']);
        return $app->redirect($app["url_generator"]->generate("Panier.index"));
    }

    public function deletePanier(Application $app,$id){
        $user = $app['session']->get('user_id');

        $this->panierModel = new PanierModel($app);
        $panierModel = $this->panierModel->getProduitDansPanierById($id);
        $produit_id = $this->panierModel->getProuidtIDByID($id);

        if ($panierModel['quantite'] == 1){
            var_dump($panierModel['quantite']);
            $panierModel = $this->panierModel->supprimerProduitDuPanier($id);
        }else{
            $panierModel['quantite'] = $panierModel['quantite'] - 1;
            $panierModel = $this->panierModel->modifierQuantitePanier($panierModel['produit_id'],$panierModel,$user);
        }
        $this->produitModel = new ProduitModel($app);
        $stock = $this->produitModel->getStockByID($id);
        if ($stock['stock'] == null){
            $stock['stock'] = 0;
        }
        $this->produitModel->updateStock($produit_id['produit_id'],$stock['stock']+1);

        return $app->redirect($app["url_generator"]->generate("Panier.index"));
    }

    public function detailsProduit(Application $app,$id){
        $this->produitModel = new ProduitModel($app);
        $produitModel = $this->produitModel->getProduit($id);

        return $app["twig"]->render("frontOff/produit/detailsProduit.html.twig",['produits'=>$produitModel]);
    }

    public function showCommande(Application $app,$id){
        $user = $app['session']->get('user_id');
        $this->commandesModel = new CommandesModel($app);
        $verification = $this->commandesModel->getUserIDByCommande($user,$id);

        if (!($app['session']->get('roles') == 'ROLE_CLIENT' && (int)$verification['user_id'] == (int)$user)){
            return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
        }

        $this->panierModel = new PanierModel($app);
        $commandeDetails = $this->panierModel->getCommandeByID($user,$id);

        return $app["twig"]->render("frontOff/commande/showDetailsCommandesByID.html.twig",['produits'=>$commandeDetails]);
    }

    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\PanierController::showPagePanierProduits')->bind('Panier.index');
        $index->get("/add/{id}", 'App\Controller\PanierController::addPanier')->bind('Panier.add');
        $index->match("/delete/{id}", 'App\Controller\PanierController::deletePanier')->bind('Panier.delete');
        $index->match("/details/{id}", 'App\Controller\PanierController::detailsProduit')->bind('Panier.details');
        $index->match("/showCommande/{id}", 'App\Controller\PanierController::showCommande')->bind('Panier.showCommande');
        return $index;
    }
}