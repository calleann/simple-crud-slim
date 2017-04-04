<?php
  namespace Test\Controllers;
  use Test\Models\User;

  /**
   *
   */
  class HomeController extends Controller
  {
    public function index($request,$response)
    {
      return $this->view->render($response,'home.twig');
    }
  }
