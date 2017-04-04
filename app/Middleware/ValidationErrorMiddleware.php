<?php

  namespace Test\Middleware;
  /**
   *
   */
  class ValidationErrorMiddleware extends Middleware
  {

    public function __invoke($request,$response,$next)
    {
      if (isset($_SESSION['error'])) {
        $env = $this->view->getEnvironment();
        $env->addGlobal('error',$_SESSION['error']);
        unset($_SESSION['error']);
      }
      $response = $next($request,$response);
      return $response;
    }
  }
