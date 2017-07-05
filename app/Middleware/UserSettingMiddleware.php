<?php

  namespace Test\Middleware;
  /**
   *
   */
  class UserSettingMiddleware extends Middleware
  {

    public function __invoke($request,$response,$next)
    {
      //Needs some refractoring see if a could store USER in the Session
      if (isset($_SESSION['id']) && isset($_SESSION['type']) && isset($_SESSION['name'])) {
        $env = $this->view->getEnvironment();
        $env->addGlobal('id',$_SESSION['id']);
        $env->addGlobal('type',$_SESSION['type']);
        $env->addGlobal('name',$_SESSION['name']);
      }
      $response = $next($request,$response);
      return $response;
    }
  }
