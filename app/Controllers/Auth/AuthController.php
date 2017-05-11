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
        'cin'=>v::noWhitespace()->notEmpty()->Alnum(),
        'num_tel'=>v::noWhitespace()->notEmpty()->Digit(),
        'password'=>v::noWhitespace()->notEmpty()
      ]);
      if($validation->failed()){
        return $response->withRedirect($this->router->pathFor('auth.signup'));
      }
      $user = User::create([
        'name'=>$request->getParam('name'),
        'email'=>$request->getParam('email'),
        'num_tel'=>$request->getParam('num_tel'),
        'cin'=>$request->getParam('cin'),
        'password'=>password_hash($request->getParam('password'),PASSWORD_DEFAULT),
        'type'=>$request->getParam('type')
      ]);
      $dir = __DIR__.'/../../../dossiers/'.$user['id'];
      //echo $dir;
      mkdir($dir,0777,true);
      Demande::create([
        'Num_dossier'=>$user['id'],
        'statut'=>"nouveau"
      ]);
      $dossier = Dossier::create([
        'statut'=>"nouveau"
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
      //die();
      return $response->withRedirect($this->router->pathFor('home'));
    }
    public function getSignout($request,$response){
      unset($_SESSION['id']);
      unset($_SESSION['type']);
      return $response->withRedirect($this->router->pathFor('auth.signin'));
    }
  }
