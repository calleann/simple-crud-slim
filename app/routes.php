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

    // //user commandes
    $app->get('/test','DemandeController:getTest');
    $app->get('/test2/{id}','DemandeController:getTest2')->setName('demande.test2');
    // $app->get('/test3/{id}','DemandeController:getDemandeDetails');

    $app->get('/test4','DemandeController:getTestform')->setName('demande.test');
    $app->post('/test4','DemandeController:postTestform');

    $app->get('/test5','DemandeController:getTestSoumettre')->setName('demande.submit');
    $app->post('/test5','DemandeController:postTestSoumettre');
