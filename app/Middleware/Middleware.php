<?php

  namespace Test\Middleware;

  /**
   *
   */
  class Middleware
  {
    protected $container;
    function __construct($container)
    {
      $this->container = $container;
    }
    function __get($prop){
      if($this->container->{$prop}){
        return $this->container->{$prop};
      }
    }
  }
