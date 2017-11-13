<?php
namespace App\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

use App\Model\clientModel;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
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
        if (isset($_POST['nom']) && isset($_POST['typeProduit_id']) and isset($_POST['nom']) and isset($_POST['photo']) and isset($_POST['id'])) {
            $donnees = [
                'nom' => htmlspecialchars($_POST['nom']),                    // echapper les entrées
                'typeProduit_id' => htmlspecialchars($req->get('typeProduit_id')),  //$app['request']-> ne focntionne plus
                'prix' => htmlspecialchars($req->get('prix')),
                'photo' => $app->escape($req->get('photo')),  //$req->query->get('photo')-> ne focntionne plus
                'id' => $app->escape($req->get('id'))//$req->query->get('photo')
            ];
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nom']))) $erreurs['nom']='nom composé de 2 lettres minimum';
            if(! is_numeric($donnees['typeProduit_id']))$erreurs['typeProduit_id']='veuillez saisir une valeur';
            if(! is_numeric($donnees['prix']))$erreurs['prix']='saisir une valeur numérique';
            if (! preg_match("/[A-Za-z0-9]{2,}.(jpeg|jpg|png)/",$donnees['photo'])) $erreurs['photo']='nom de fichier incorrect (extension jpeg , jpg ou png)';
            if(! is_numeric($donnees['id']))$erreurs['id']='saisir une valeur numérique';
            $contraintes = new Assert\Collection(
                [
                    'id' => [new Assert\NotBlank(),new Assert\Type('digit')],
                    'typeProduit_id' => [new Assert\NotBlank(),new Assert\Type('digit')],
                    'nom' => [
                        new Assert\NotBlank(['message'=>'saisir une valeur']),
                        new Assert\Length(['min'=>2, 'minMessage'=>"Le nom doit faire au moins {{ limit }} caractères."])
                    ],
                    //http://symfony.com/doc/master/reference/constraints/Regex.html
                    'photo' => [
                        new Assert\Length(array('min' => 5)),
                        new Assert\Regex([ 'pattern' => '/[A-Za-z0-9]{2,}.(jpeg|jpg|png)/',
                            'match'   => true,
                            'message' => 'nom de fichier incorrect (extension jpeg , jpg ou png)' ]),
                    ],
                    'prix' => new Assert\Type(array(
                        'type'    => 'numeric',
                        'message' => 'La valeur {{ value }} n\'est pas valide, le type est {{ type }}.',
                    ))
                ]);
            $errors = $app['validator']->validate($donnees,$contraintes);  // ce n'est pas validateValue

            if (count($errors) > 0) {
                $this->typeProduitModel = new TypeProduitModel($app);
                $typeProduits = $this->typeProduitModel->getAllTypeProduits();
                return $app["twig"]->render('backOff/Produit/editProduit.html.twig',['donnees'=>$donnees,'errors'=>$errors,'erreurs'=>$erreurs,'typeProduits'=>$typeProduits]);
            }
            else
            {
                $this->ProduitModel = new ProduitModel($app);
                $this->ProduitModel->updateProduit($donnees);
                return $app->redirect($app["url_generator"]->generate("produit.index"));
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