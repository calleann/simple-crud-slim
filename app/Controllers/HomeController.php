<?php
  namespace Test\Controllers;
  use Test\Model\User;

  /**
   *
   */
  class HomeController extends Controller
  {
    public function index($request,$response)
    {
      if(isset($_SESSION['id']))
      {
        $user = User::find($_SESSION['id']);
        $env = $this->view->getEnvironment();
        $env->addGlobal('notifications',$user->notifications);
      }
      return $this->view->render($response,'home.html.twig');
    }

    public function getfoo($request,$response)
    {
      $this->flash->addMessage('Test', 'This is a message');
      return $response->withRedirect($this->router->pathFor('bar'));
    }
    public function getbar($request,$response)
    {
      $messages = $this->flash->getMessages();
      if(!empty($messages)){
        print_r($messages['Test'][0]);
      }
      else {
        echo 'nothing to show here';
      }
    }
  }
