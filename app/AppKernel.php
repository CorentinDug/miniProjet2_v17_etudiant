<?php
require "config.php";

//On initialise le timeZone
ini_set('date.timezone', 'Europe/Paris');

//On ajoute l'autoloader (compatible winwin)
$loader = require_once join(DIRECTORY_SEPARATOR,[dirname(__DIR__), 'vendor', 'autoload.php']);

//dans l'autoloader nous ajoutons notre répertoire applicatif
$loader->addPsr4('App\\',join(DIRECTORY_SEPARATOR,[dirname(__DIR__), 'src']));

//Nous instancions un objet Silex\Application
$app = new Silex\Application();

// connexion à la base de données
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'dbhost' => hostname,
        'host' => hostname,
        'dbname' => database,
        'user' => username,
        'password' => password,
        'charset'   => 'utf8mb4',
    ),
));

//utilisation de twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => join(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'src', 'View'])
));

// utilisation des sessoins
$app->register(new Silex\Provider\SessionServiceProvider());

//en dev, nous voulons voir les erreurs
$app['debug'] = true;

// rajoute la méthode asset dans twig

$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.named_packages' => array(
        'css' => array(
            'version' => 'css2',
            'base_path' => __DIR__.'/../web/'
        ),
    ),
));

// par défaut les méthodes DELETE PUT ne sont pas prises en compte
use Symfony\Component\HttpFoundation\Request;
Request::enableHttpMethodParameterOverride();

//validator      => php composer.phar  require symfony/validator
$app->register(new Silex\Provider\ValidatorServiceProvider());

//Permet d'utiliser les tokens
use Silex\Provider\CsrfServiceProvider;
$app->register(new CsrfServiceProvider());
use Silex\Provider\FormServiceProvider;
$app->register(new FormServiceProvider());

// Montage des controleurs sur le routeur
include('routing.php');

//MiddleWare routes cote ADMIN
$app->before(function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
    $nomRoute = $request->get('_route');
    $routeAdmin = array("commentaires.validFormDeleteCommentaire","commentaires.delete","commentaire.showCommentaire","produit.validFormEditProduit",
        "produit.editProduit","produit.validFormDeleteProduit","produit.deleteProduit","produit.validFormAddProduit","produit.addProduit","produit.showProduits","client.validFormDelete",
        "client.delete","client.updateByAdmin","client.validFormAddClient","client.addClient","client.showAll",
        "commande.voirCommande","commande.modifierEtat","commande.showAllCommandes","index.pageAdmin");

    if ($app['session']->get('roles') != 'ROLE_ADMIN' && in_array($nomRoute,$routeAdmin)){
        return $app->redirect($app["url_generator"]->generate('index.erreurDroit'));
    }

    if (($app['session']->get('roles') != 'ROLE_ADMIN' && $app['session']->get('roles') != 'ROLE_CLIENT')  && $nomRoute=="client.validFormEditClient") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
});

//MiddleWare routes cote CLIENT
$app->before(function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
    $nomRoute = $request->get('_route');
    $routeClient = array("commentaire.showCommentairesClient","commentaires.validFormAddCommentaire","commentaire.add","client.update",
        "commande.valider","commande.index","produitClient.find","Panier.showCommande","Panier.details","Panier.delete","Panier.add",
        "Panier.index");

    if ($app['session']->get('roles') != 'ROLE_CLIENT' && in_array($nomRoute,$routeClient)){
        return $app->redirect($app["url_generator"]->generate('index.erreurDroit'));
    }

    if (($app['session']->get('logged') != 1 && $nomRoute=='client.index')){
        return $app->redirect($app["url_generator"]->generate('index.erreurDroit'));
    }
});

//MiddleWares TOKEN
use Symfony\Component\Security\Csrf\CsrfToken;
$app->before(function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {

    $nomRoute=$request->get('_route');
    $routeToken = array("user.validFormlogin","produit.validFormAddProduit","produit.validFormEditProduit","produit.validFormDeleteProduit",
        "client.validFormAddClient","client.validFormDelete","client.validFormEditClient","commentaires.validFormAddCommentaire",
        "commentaires.validFormDeleteCommentaire","client.validFormAddClientNonInscrit");

    if (in_array($nomRoute,$routeToken)){
        if (isset($_POST['_csrf_token'])) {
            $token = $_POST['_csrf_token'];
            $csrf_token = new CsrfToken('token', $token);
            $csrf_token_ok = $app['csrf.token_manager']->isTokenValid($csrf_token);
            if(!$csrf_token_ok)
            {
                return $app ->redirect($app["url_generator"]->generate("index.errorCsrf"));
            }
        }
    }
});

//On lance l'application
$app->run();