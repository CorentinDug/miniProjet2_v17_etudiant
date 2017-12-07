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

//MiddleWare route pour le cote ADMIN
$app->before(function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
    $nomRoute=$request->get("_route");
    //Bloquer la page de l'administration aux anonymes
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $nomRoute=="index.pageAdmin") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    //Bloquer les routes des commandes
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $nomRoute=="commande.showAllCommandes") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $nomRoute=="commande.modifierEtat") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $nomRoute=="commande.voirCommande") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    //Bloquer la route des clients pour ajouter, modifier et supprimer ceux-ci
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $nomRoute=="client.showAll") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') == 'ROLE_CLIENT'  && $nomRoute=="client.addClient") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') == 'ROLE_CLIENT'  && $nomRoute=="client.validFormAddClient") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $nomRoute=="client.updateByAdmin") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if (($app['session']->get('roles') != 'ROLE_ADMIN' && $app['session']->get('roles') != 'ROLE_CLIENT')  && $nomRoute=="client.validFormEditClient") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $nomRoute=="client.delete") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $nomRoute=="client.validFormDelete") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    //Bloquer l'acces aux autres qu'admin pour les produits
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $nomRoute=="produit.showProduits") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $nomRoute=="produit.addProduit") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $nomRoute=="produit.validFormAddProduit") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $nomRoute=="produit.deleteProduit") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $nomRoute=="produit.validFormDeleteProduit") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $nomRoute=="produit.editProduit") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $nomRoute=="produit.validFormEditProduit") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $nomRoute=="commentaire.showCommentaire") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $nomRoute=="commentaires.delete") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $nomRoute=="commentaires.validFormDeleteCommentaire") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
});

//MiddleWare route pour le cote CLIENT
$app->before(function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
    $nomRoute=$request->get("_route");
    //Bloquer la page de l'administration aux anonymes
    if ($app['session']->get('roles') != 'ROLE_CLIENT'  && $nomRoute=="Panier.index") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_CLIENT'  && $nomRoute=="Panier.add") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_CLIENT'  && $nomRoute=="Panier.delete") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_CLIENT'  && $nomRoute=="Panier.details") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_CLIENT'  && $nomRoute=="Panier.showCommande") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_CLIENT'  && $nomRoute=="produitClient.find") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_CLIENT'  && $nomRoute=="commande.index") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_CLIENT'  && $nomRoute=="commande.valider") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_CLIENT'  && $nomRoute=="client.update") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_CLIENT'  && $nomRoute=="commentaire.add") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_CLIENT'  && $nomRoute=="commentaires.validFormAddCommentaire") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
    if ($app['session']->get('roles') != 'ROLE_CLIENT'  && $nomRoute=="commentaire.showCommentairesClient") {
        return $app->redirect($app["url_generator"]->generate("index.erreurDroit"));
    }
});

//MiddleWares TOKEN
use Symfony\Component\Security\Csrf\CsrfToken;
$app->before(function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {

    $nomRoute=$request->get('_route');
    if ($nomRoute == 'user.validFormlogin'){
        if (isset($_POST['_csrf_token'])) {
            $token = $_POST['_csrf_token'];
            $csrf_token = new CsrfToken('token_user_login', $token);
            $csrf_token_ok = $app['csrf.token_manager']->isTokenValid($csrf_token);
            if(!$csrf_token_ok)
            {
                return $app ->redirect($app["url_generator"]->generate("index.errorCsrf"));
            }
        }
    }else if($nomRoute == 'produit.validFormAddProduit'){
        if (isset($_POST['_csrf_token'])) {
            $token = $_POST['_csrf_token'];
            $csrf_token = new CsrfToken('token_add_produit', $token);
            $csrf_token_ok = $app['csrf.token_manager']->isTokenValid($csrf_token);
            if(!$csrf_token_ok)
            {
                return $app ->redirect($app["url_generator"]->generate("index.errorCsrf"));
            }
        }
    }else if($nomRoute == 'produit.validFormEditProduit'){
        if (isset($_POST['_csrf_token'])) {
            $token = $_POST['_csrf_token'];
            $csrf_token = new CsrfToken('token_edit_produit', $token);
            $csrf_token_ok = $app['csrf.token_manager']->isTokenValid($csrf_token);
            if(!$csrf_token_ok)
            {
                return $app ->redirect($app["url_generator"]->generate("index.errorCsrf"));
            }
        }
    }else if($nomRoute == 'produit.validFormDeleteProduit'){
        if (isset($_POST['_csrf_token'])) {
            $token = $_POST['_csrf_token'];
            $csrf_token = new CsrfToken('token_delete_produit', $token);
            $csrf_token_ok = $app['csrf.token_manager']->isTokenValid($csrf_token);
            if(!$csrf_token_ok)
            {
                return $app ->redirect($app["url_generator"]->generate("index.errorCsrf"));
            }
        }
    }else if($nomRoute == 'client.validFormAddClient'){
        if (isset($_POST['_csrf_token'])) {
            $token = $_POST['_csrf_token'];
            $csrf_token = new CsrfToken('token_add_client', $token);
            $csrf_token_ok = $app['csrf.token_manager']->isTokenValid($csrf_token);
            if(!$csrf_token_ok)
            {
                return $app ->redirect($app["url_generator"]->generate("index.errorCsrf"));
            }
        }
    }else if($nomRoute == 'client.validFormDelete'){
        if (isset($_POST['_csrf_token'])) {
            $token = $_POST['_csrf_token'];
            $csrf_token = new CsrfToken('token_delete_client', $token);
            $csrf_token_ok = $app['csrf.token_manager']->isTokenValid($csrf_token);
            if(!$csrf_token_ok)
            {
                return $app ->redirect($app["url_generator"]->generate("index.errorCsrf"));
            }
        }
    }else if($nomRoute == 'client.validFormEditClient'){
        if (isset($_POST['_csrf_token'])) {
            $token = $_POST['_csrf_token'];
            $csrf_token = new CsrfToken('token_update_client', $token);
            $csrf_token_ok = $app['csrf.token_manager']->isTokenValid($csrf_token);
            if(!$csrf_token_ok)
            {
                return $app ->redirect($app["url_generator"]->generate("index.errorCsrf"));
            }
        }
    }else if($nomRoute == 'commentaires.validFormAddCommentaire'){
        if (isset($_POST['_csrf_token'])) {
            $token = $_POST['_csrf_token'];
            $csrf_token = new CsrfToken('token_add_commentaires', $token);
            $csrf_token_ok = $app['csrf.token_manager']->isTokenValid($csrf_token);
            if(!$csrf_token_ok)
            {
                return $app ->redirect($app["url_generator"]->generate("index.errorCsrf"));
            }
        }
    }else if($nomRoute == 'commentaires.validFormDeleteCommentaire'){
        if (isset($_POST['_csrf_token'])) {
            $token = $_POST['_csrf_token'];
            $csrf_token = new CsrfToken('token_delete_commentaires', $token);
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