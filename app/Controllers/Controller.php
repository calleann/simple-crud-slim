<?php

  namespace Test\Controllers;

  /**
   *
   */
  class Controller
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
