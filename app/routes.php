<?php

  // $app->get('/home',function ($request,$response){
  //     return $this->view->render($response,'home.twig');
  // });

    $app->get('/','HomeController:index')->setName('home');
    //sign in
    $app->get('/auth/signin','AuthController:getSignin')->setName('auth.signin');
    $app->post('/auth/signin','AuthController:postSignin');
    //signup
    $app->get('/auth/signup','AuthController:getSignup')->setName('auth.signup');
    $app->post('/auth/signup','AuthController:postSignup');
    //signout
    $app->get('/auth/signout','AuthController:getSignout')->setName('auth.signout');

    //user commandes
    $app->get('/test','DemandeController:getAllDemandes');
    $app->get('/test2/{id}','DemandeController:getDemandesByUser');
    $app->get('/test3/{id}','DemandeController:getDemandeDetails');
