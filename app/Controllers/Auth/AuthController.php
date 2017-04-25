<?php
  namespace Test\Controllers\Auth;
  use Test\Controllers\Controller;
  use Test\Models\User;
  use Respect\Validation\Validator as v;

  /**
   *
   */
  class AuthController extends Controller
  {
    public function getSignin($request,$response)
    {
        return $this->view->render($response,'auth/signin.twig');
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
        return $this->view->render($response,'auth/signup.twig');
    }
    public function postSignup($request,$response)
    {
      $validation = $this->validator->validate($request,[
        'email'=> v::noWhitespace()->notEmpty()->email()->emailAvailable(),
        'name'=>v::notEmpty()->alpha()->nameAvailable(),
        'cin'=>v::noWhitespace()->notEmpty(),
        'num_tel'=>v::noWhitespace()->notEmpty(),
        'password'=>v::noWhitespace()->notEmpty()
      ]);
      if($validation->failed()){
        return $response->withRedirect($this->router->pathFor('auth.signup'));
      }
      User::create([
        'name'=>$request->getParam('name'),
        'email'=>$request->getParam('email'),
        'num_tel'=>$request->getParam('num_tel'),
        'cin'=>$request->getParam('cin'),
        'password'=>password_hash($request->getParam('password'),PASSWORD_DEFAULT),
        'type'=>$request->getParam('type')
      ]);
      return $response->withRedirect($this->router->pathFor('home'));
    }
    public function getSignout($request,$response){
      unset($_SESSION['id']);
      unset($_SESSION['type']);
      return $response->withRedirect($this->router->pathFor('home'));
    }
  }
