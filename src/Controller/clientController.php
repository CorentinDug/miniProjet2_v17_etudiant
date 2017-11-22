<?php
namespace App\Controller;

use App\Model\PanierModel;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

use App\Model\clientModel;

use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Security;
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
        $this->clientModel = new clientModel($app);
        $donnees = $this->clientModel->getCoordonneesClientById($id);
        return $app["twig"]->render('frontOff/client/editClientCoordonnees.html.twig',['donnees'=>$donnees]);
    }

    public function validFormEditClient(Application $app, Request $req) {
        if (isset($_POST['nom']) && isset($_POST['username']) and isset($_POST['code_postal']) and isset($_POST['adresse']) and isset($_POST['id']) and isset($_POST['ville'])) {
            $donnees = [
                'nom' => htmlspecialchars($_POST['nom']),                    // echapper les entrées
                'username' => htmlspecialchars($req->get('username')),  //$app['request']-> ne focntionne plus
                'email' => htmlspecialchars($req->get('email')),  //$app['request']-> ne focntionne plus
                'code_postal' => htmlspecialchars($req->get('code_postal')),
                'ville' => htmlspecialchars($req->get('ville')),  //$req->query->get('photo')-> ne focntionne plus
                'adresse' => htmlspecialchars($req->get('adresse')),  //$req->query->get('photo')-> ne focntionne plus
                'id' => $app->escape($req->get('id'))//$req->query->get('photo')
            ];

            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nom']))) $erreurs['nom']='Le nom doit être composé de 2 lettres minimum';
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['username']))) $erreurs['username']='Lepseudo doit être composé de 2 lettres minimum';
            if (!(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $donnees['email']))) $erreurs['email']='E-Mail : xCaracteres@yCaracteres.zCaracteres';
            if ((! preg_match("/^[0-9]{5}/",$donnees['code_postal']))) $erreurs['code_postal']='Le code postal doit être composé de 5 chiffres';
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['adresse']))) $erreurs['adresse']="L'adresse doit être composé de 2 lettres minimum";
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['ville']))) $erreurs['ville']='La ville doit être composé de 2 lettres minimum';
            if(! is_numeric($donnees['code_postal']))$erreurs['code_postal']='Saisir une valeur numérique';

            if (!empty($erreurs)) {
                $this->clientModel = new clientModel($app);
                $typeProduits = $this->clientModel->getCoordonneesClientById($donnees['id']);
                return $app["twig"]->render('frontOff/client/editClientCoordonnees.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs]);
            }
            else
            {
                $this->clientModel = new clientModel($app);
                //var_dump($donnees);
                $this->clientModel->editClient($donnees);
                if ($app['session']->get('roles') == 'ROLE_ADMIN'){
                    return $app->redirect($app["url_generator"]->generate("client.showAll"));
                }else{
                    return $app->redirect($app["url_generator"]->generate("client.index"));
                }
            }
        }
        else
            return $app->abort(404, 'error Pb id form edit');
    }

    public function voirTousLesClients(Application $app){
        $this->clientModel = new clientModel($app);
        $donneesClient = $this->clientModel->getAllClient();

        return $app["twig"]->render('backOff/client/showAllClients.html.twig',['donneesClient'=>$donneesClient]);
    }

    public function ajouterUnClient(Application $app){
        return $app["twig"]->render('backOff/client/creerClient.html.twig');
    }

    public function validFormAjouterClient(Application $app,Request $req){
        if (isset($_POST['username']) && isset($_POST['motdepasse']) and isset($_POST['email']) and isset($_POST['nom']) and isset($_POST['code_postal']) and isset($_POST['ville']) and isset($_POST['adresse'])) {
            $donnees = [
                'username' => htmlspecialchars($req->get('username')),
                'motdepasse' => htmlspecialchars($req->get('motdepasse')),
                'email' => htmlspecialchars($req->get('email')),
                'nom' => htmlspecialchars($req->get('nom')),
                'code_postal' => htmlspecialchars($req->get('code_postal')),
                'ville' => htmlspecialchars($req->get('ville')),
                'adresse' => htmlspecialchars($req->get('adresse')),
            ];

            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nom']))) $erreurs['nom']='Le nom doit être composé de 2 lettres minimum';
            if (strlen($donnees['motdepasse']) < 4) $erreurs['motdepasse']='le mot de passe doit contenir quatre caracteres minimum';
            if (strlen($donnees['username']) < 4) $erreurs['username']='Le pseudo doit être composé de 4 caracteres minimum';
            if (!(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $donnees['email']))) $erreurs['email']='E-Mail : xCaracteres@yCaracteres.zCaracteres';
            if ((! preg_match("/^[0-9]{5}/",$donnees['code_postal']))) $erreurs['code_postal']='Le code postal doit être composé de 5 chiffres';
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['adresse']))) $erreurs['adresse']="L'adresse doit être composé de 2 lettres minimum";
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['ville']))) $erreurs['ville']='La ville doit être composé de 2 lettres minimum';
            if(! is_numeric($donnees['code_postal']))$erreurs['code_postal']='Saisir une valeur numérique';

            if(! empty($erreurs))
            {
                return $app["twig"]->render('backOff/client/creerClient.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs]);
            }
            else
            {
                $this->clientModel = new clientModel($app);
                $this->clientModel->addClient($donnees);
                return $app->redirect($app["url_generator"]->generate("client.showAll"));
            }

        }
        else
            return $app->abort(404, 'error Pb data form Add');
    }

    public function supprimerUnClient(Application $app,$id){
        $this->clientModel = new clientModel($app);
        $donneesClient = $this->clientModel->getCoordonneesClientById($id);
        return $app['twig']->render('backOff/client/supprimerClient.html.twig',['donneesClient'=>$donneesClient]);
    }

    public function validFormSupprimerClient(Application $app,Request $req){
        $donnees=[
            'id' => $req->get('id')
        ];
        $this->clientModel = new clientModel($app);
        $this->clientModel->deleteClient($donnees['id']);
        return $app->redirect($app["url_generator"]->generate("client.showAll"));
    }

    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\clientController::showCoordonneesClient')->bind('client.index');
        $index->match("/showAllClient", 'App\Controller\clientController::voirTousLesClients')->bind('client.showAll');
        $index->get("/addClient", 'App\Controller\clientController::ajouterUnClient')->bind('client.addClient');
        $index->post("/addClient", 'App\Controller\clientController::validFormAjouterClient')->bind('client.validFormAddClient');
        $index->get("/update/{id}", 'App\Controller\clientController::modifierCoordonnees')->bind('client.update');
        $index->put("/update/", 'App\Controller\clientController::validFormEditClient')->bind('client.validFormEditClient');
        $index->get("/deleteClient/{id}", 'App\Controller\clientController::supprimerUnClient')->bind('client.delete');
        $index->delete("/deleteClient/", 'App\Controller\clientController::validFormSupprimerClient')->bind('client.validFormDelete');
        return $index;
    }

}