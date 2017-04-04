<?php
  require __DIR__.'/../vendor/autoload.php';
  use Respect\Validation\Validator as v;
  session_start();

  $app = new \Slim\App([
    'settings'=>[
      'displayErrorDetails'=>true,
      'db'=>[
        'driver'=>'mysql',
        'host'=>'localhost',
        'database'=>'app',
        'username'=>'root',
        'password'=>'',
        'charset'=>'utf8',
        'collation'=>'utf8_unicode_ci',
        'prefix'=>''
    ]
  ],
  ]);

  $container = $app->getContainer();
  $capsule = new \Illuminate\Database\Capsule\Manager;
  $capsule->addConnection($container['settings']['db']);
  $capsule->setAsGlobal();
  $capsule->bootEloquent();

  $container['db'] = function($container) use ($capsule){
    return $capsule;
  };

  $container['view'] = function($container){
    $view = new \Slim\Views\Twig(__DIR__.'/../resources/views',[
      'cache' => false,
    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(
      $container->router,
      $container->request->getUri()
    ));
    return $view;
  };

  $container['validator'] = function($container){
    return new \Test\Validation\Validator;
  };
  $app->add(new \Test\Middleware\ValidationErrorMiddleware($container));
  // $app->add(new \Test\Middleware\EmailcheckMiddleware($container));

  //dump the controllers into the container
  $container['HomeController'] = function($container){
    return new \Test\Controllers\HomeController($container);
  };
  $container['AuthController'] = function($container){
    return new \Test\Controllers\Auth\AuthController($container);
  };

  $container['DemandeController'] = function($container){
    return new \Test\Controllers\Commandes\DemandeController($container);
  };
  //set of custom rules
  v::with('Test\\Validation\\Rules');

  require __DIR__.'/../app/routes.php';
