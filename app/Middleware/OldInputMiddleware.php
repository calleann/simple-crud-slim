<?php

  namespace Test\Middleware;
  /**
   *
   */
  class OldInputMiddleware extends Middleware
  {

    public function __invoke($request,$response,$next)
    {
      if (isset($_SESSION['old']))
      {
        $env = $this->view->getEnvironment();
        $env->addGlobal('old',$_SESSION['old']);
      }
      $_SESSION['old'] = $request->getParams();
      $response = $next($request,$response);
      return $response;
    }
  }
