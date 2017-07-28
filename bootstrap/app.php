<?php
  require __DIR__.'/../vendor/autoload.php';


  use Slim\Handlers\NotFound;
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

  $container['cred'] = function($container){
    $cred = array(
      'host'=>'localhost',
      'database'=>'app',
      'username'=>'root',
      'password'=>''
    );
    return $cred;
  };

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

  $container['mail'] = function($container){
    $mail =  new PHPMailer;
    //configuration SMTP
    $mail->isSMTP();
  	$mail->SMTPDebug = 0;
  	$mail->Debugoutput = 'html';
  	$mail->Host = 'smtp.gmail.com';
  	$mail->Port = 587;
  	$mail->SMTPSecure = 'tls';
  	$mail->SMTPAuth = true;
  	$mail->Username = 'testanass12345@gmail.com';
  	$mail->Password = 'azeqsd123.';
    $mail->setFrom('testanass12345@gmail.com', 'message bot');
    $mail->addReplyTo('testanass12345@gmail.com', 'message bot');
    return $mail;
  };

  $container['validator'] = function($container){
    return new \Test\Validation\Validator;
  };

  $container['flash'] = function($container){
    return new \Slim\Flash\Messages;
  };
  $container['pdf'] = function($container){
    return new \fpdf\FPDF();
  };
  //Middleware
  $app->add(new \Test\Middleware\ValidationErrorMiddleware($container));
  $app->add(new \Test\Middleware\OldInputMiddleware($container));
  $app->add(new \Test\Middleware\UserSettingMiddleware($container));

  // $app->add(new \Test\Middleware\EmailcheckMiddleware($container));

  //dump the controllers into the container
  $container['HomeController'] = function($container){
    return new \Test\Controllers\HomeController($container);
  };
  $container['AuthController'] = function($container){
    return new \Test\Controllers\Auth\AuthController($container);
  };

  $container['nomclature_construction'] = function($container){
    $nomclature_construction = array(
      'demande_autorisation' => "demande d'autorisation" ,
      'propriete'=> "propriété",
      'plan_topographique' => "plan topographique",
      'maitrise_oeuvre' => "maitrise d'oeuvre",
      'plan_structure' => " plan de structure",
      'note_pressentation' => "note de présentation",
      'architecte' => "architecte",
      'fiche_technique' => "fiche technique",
      'bureau_controle' => "bureau de controle",
      'notice_securite' => "notice de sécurité",
      'contrat' => "contrat",
      'cahier_chantier' => "cahier de chantier",
      'fiche_sapeurs' => "fiche sapeurs"
    );
    return $nomclature_construction;
  };

  $container['generateRandomString'] = function($container){
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);
  };
  //Not Allowed handling
  $container['NotAllowedHandler'] = function ($container) {
      return function ($request, $response) use ($container) {
          return $container['response']
              ->withStatus(405)
              ->withHeader('Content-Type', 'text/html')
              ->write('Not Allowed to snoop around');
      };
  };

  $container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        return $container['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('Page not found');
    };
};


  $container['DemandeController'] = function($container){
    return new \Test\Controllers\Commandes\DemandeController($container);
  };

  $container['AdminController'] = function($container){
    return new \Test\Controllers\Admin\AdminController($container);
  };
  //set of custom rules
  v::with('Test\\Validation\\Rules');

  require __DIR__.'/../app/routes.php';
