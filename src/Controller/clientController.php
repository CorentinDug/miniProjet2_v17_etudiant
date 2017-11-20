<?php
namespace App\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

use App\Model\clientModel;

use Symfony\Component\Validator\Constraints as Assert;
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
                var_dump($donnees);
                $this->clientModel->editClient($donnees);
                return $app->redirect($app["url_generator"]->generate("client.index"));
            }
        }
        else
            return $app->abort(404, 'error Pb id form edit');
    }

    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\clientController::showCoordonneesClient')->bind('client.index');
        $index->get("/update/{id}", 'App\Controller\clientController::modifierCoordonnees')->bind('client.update');
        $index->put("/update/", 'App\Controller\clientController::validFormEditClient')->bind('client.validFormEditClient');
        return $index;
    }

}