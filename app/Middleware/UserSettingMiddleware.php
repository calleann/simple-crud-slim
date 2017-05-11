<?php

  namespace Test\Middleware;
  /**
   *
   */
  class UserSettingMiddleware extends Middleware
  {

    public function __invoke($request,$response,$next)
    {
      if (isset($_SESSION['id'])&&isset($_SESSION['type'])) {
        $env = $this->view->getEnvironment();
        $env->addGlobal('id',$_SESSION['id']);
        $env->addGlobal('type',$_SESSION['type']);
      }
      $response = $next($request,$response);
      return $response;
    }
  }
