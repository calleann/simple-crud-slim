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
      return $this->view->render($response,'home.html.twig');
    }
  }
