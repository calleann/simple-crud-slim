<?php

  namespace Test\Middleware;
  use Test\Models\User;
  /**
   *
   */
  class EmailcheckMiddleware extends Middleware
  {
    public function __invoke($request,$response,$next)
    {
      
      $response = $next($request,$response);
      return $response;
    }
  }
