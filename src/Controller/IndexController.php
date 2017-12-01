<?php
namespace App\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;   // modif version 2.0


class IndexController implements ControllerProviderInterface
{
    public function index(Application $app)
    {
        if ($app['session']->get('roles') == 'ROLE_CLIENT')
             return $app->redirect($app["url_generator"]->generate("Panier.index"));
        if ($app['session']->get('roles') == 'ROLE_ADMIN')
            return $app->redirect($app["url_generator"]->generate("index.pageAdmin"));
            //return $app["twig"]->render("backOff/backOFFICE.html.twig");

        return $app["twig"]->render("accueil.html.twig");
    }

    public function erreurDroit(Application $app){
        return $app["twig"]->render("erreurDroit.html.twig");
    }

    public function erreurCrsf(Application $app){
        return $app["twig"]->render("error_csrf.html.twig");
    }

    public function showPageAdmin(Application $app){
        return $app["twig"]->render("backOff\backOFFICE.html.twig");
    }

    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\IndexController::index')->bind('accueil');
        $index->match("/pageAdmin", 'App\Controller\IndexController::showPageAdmin')->bind("index.pageAdmin");
        $index->match("/index", 'App\Controller\IndexController::index')->bind("index.index");
        $index->match("/pageError", 'App\Controller\IndexController::erreurDroit')->bind("index.erreurDroit");
        $index->match("/pageErrorToken", 'App\Controller\IndexController::erreurCrsf')->bind("index.errorCsrf");
        return $index;
    }


}
