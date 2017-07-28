<?php
  namespace Test\Controllers\Auth;
  use Test\Controllers\Controller;
  use Test\Model\User;
  use Test\Model\Demande;
  use Test\Model\Dossier;
  use Test\Model\dossier_construire;
  use Test\Model\dossier_exploitation;
  use Respect\Validation\Validator as v;

  /**
   *
   */
  class AuthController extends Controller
  {
    public function getSignin($request,$response)
    {
      if(isset($_SESSION['id']) && isset($_SESSION['type']))
        return $response->withRedirect($this->router->pathFor('home'));
      return $this->view->render($response,'auth/signin.html.twig');
    }
    public function postSignin($request,$response)
    {
      $user = User::where('email',$request->getParam('email'))->first();
      if($user)
      {
        if(password_verify($request->getParam('password'),$user->password))
        {
          $_SESSION['id'] = $user->id;
          $_SESSION['type'] = $user->type;
          $_SESSION['name'] = $user->name;
          return $response->withRedirect($this->router->pathFor('home'));
        }
      }
      return $response->withRedirect($this->router->pathFor('auth.signin'));
    }
    public function getSignup($request,$response)
    {
      if(isset($_SESSION['id']))
        return $response->withRedirect($this->router->pathFor('home'));
      return $this->view->render($response,'auth/signup.html.twig');
    }
    public function postSignup($request,$response)
    {
      $validation = $this->validator->validate($request,[
        'email'=> v::noWhitespace()->notEmpty()->email()->emailAvailable(),
        'name'=>v::notEmpty()->alpha()->nameAvailable(),
        'societe'=>v::notEmpty(),
        'cin'=>v::noWhitespace()->notEmpty()->Alnum(),
        'num_tel'=>v::noWhitespace()->notEmpty()->Digit(),
        'password'=>v::notEmpty()
      ]);
      if($validation->failed()){
        return $response->withRedirect($this->router->pathFor('auth.signup'));
      }
      //add societe
      $selector = $this->generateRandomString;
      $user = User::create([
        'name'=>$request->getParam('name'),
        'email'=>$request->getParam('email'),
        'societe'=>$request->getParam('societe'),
        'num_tel'=>$request->getParam('num_tel'),
        'cin'=>$request->getParam('cin'),
        'password'=>password_hash($request->getParam('password'),PASSWORD_DEFAULT),
        'selector'=>$selector,
        'type'=>$request->getParam('type')
      ]);
      $dir = __DIR__.'/../../../dossiers/'.$user['id'];
      //echo $dir;
      mkdir($dir,0777,true);
      // Demande::create([
      //   'Num_dossier'=>$user['id'],
      //   'statut'=>"nouveau"
      // ]);
      $dossier = Dossier::create([
        'statut'=> 0
      ]);
      if($request->getParam('type') === "construire"){
        $dossier_construire = dossier_construire::create([
          'column_construire'=>'ok'
        ]);
        $dossier_construire->dossier()->save($dossier);
      }
      if($request->getParam('type') === "exploitation"){
        $dossier_exploitation = dossier_exploitation::create([
          'column_exploitation'=>'ok'
        ]);
        $dossier_exploitation->dossier()->save($dossier);
      }
      $dossier->user()->associate($user);
      $dossier->save();
      $_SESSION['id'] = $user->id;
      $_SESSION['type'] = $user->type;
      $_SESSION['name'] = $user->name;
      //die();
      return $response->withRedirect($this->router->pathFor('home'));
    }
    public function getSignout($request,$response){
      unset($_SESSION['id']);
      unset($_SESSION['type']);
      unset($_SESSION['name']);
      return $response->withRedirect($this->router->pathFor('auth.signin'));
    }

    public function getForgetpassword($request,$response)
    {
      if(isset($_SESSION['id']) && isset($_SESSION['type']))
        return $response->withRedirect($this->router->pathFor('home'));
      $messages = $this->flash->getMessages();
      $env = $this->view->getEnvironment();
      if(!empty($messages['erreur'])){
        // var_dump($messages['erreur']);
        // die();
        $env->addGlobal('erreur',$messages['erreur'][0]);
      }
      return $this->view->render($response,'auth/forgot-password.html.twig');
    }
    public function postForgetpassword($request,$response)
    {

      echo "yay <br>";
      if(User::where('email',$request->getParam('email'))->count()){
        $user = User::where('email',$request->getParam('email'))->first();
        echo 'the link to change the password is :  /change-password/'.$user->selector;
        return  $response->withRedirect($this->router->pathFor('auth.change-password',array('selector' => $user->selector)));
      }
      else {
        $this->flash->addMessage('erreur',"this email doesn't exist in our database");
        return  $response->withRedirect($this->router->pathFor('auth.forgot-password'));
      }
      die();
    }

    public function getchangepassword($request,$response)
    {
      $messages = $this->flash->getMessages();
      if(isset($_SESSION['id']) && isset($_SESSION['type']))
        return $response->withRedirect($this->router->pathFor('home'));
      $env = $this->view->getEnvironment();
      $env->addGlobal('selector',$request->getAttribute('selector'));
      if(!empty($messages['erreur'])){
        // var_dump($messages['erreur']);
        // die();
        $env->addGlobal('erreur',$messages['erreur'][0]);
      }
      if(!empty($messages['valide'])){
        $env->addGlobal('valide',$messages['valide'][0]);
      }
      return $this->view->render($response,'auth/change-password.html.twig');
    }

    public function postchangepassword($request,$response)
    {
      echo  "yay";
      echo  $request->getParam('password')."<br>";
      echo  $request->getParam('confirm_password');
      $user = User::where('selector',$request->getAttribute('selector'))->first();
      $validation = $this->validator->validate($request,[
        'password'=>v::notEmpty(),
        'confirm_password'=>v::notEmpty()->identical($request->getParam('password'))
      ]);
      if($validation->failed()){
        $this->flash->addMessage('erreur',"Failed operation,please retry");
        return $response->withRedirect($this->router->pathFor('auth.change-password',array('selector' => $user->selector)));
      }
      $user->password = password_hash($request->getParam('password'),PASSWORD_DEFAULT);
      $user->save();
      $_SESSION['id'] = $user->id;
      $_SESSION['type'] = $user->type;
      $_SESSION['name'] = $user->name;
      return $response->withRedirect($this->router->pathFor('home'));
    }
  }
