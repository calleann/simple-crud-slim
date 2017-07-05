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

    // //Testing stuff
    $app->get('/test2','DemandeController:getTest2')->setName('demande.test2');
    // $app->get('/test3/{id}','DemandeController:getDemandeDetails');

    //front-office stuff
    $app->get('/info-supplementaire','DemandeController:getUserInfo')->setName('demande.user');
    $app->post('/info-supplementaire','DemandeController:postUserInfo');
    $app->get('/procedure-dossier','DemandeController:getTestform')->setName('demande.test');
    $app->post('/procedure-dossier','DemandeController:postTestform');

    $app->get('/soumettre-dossier','DemandeController:getTestSoumettre')->setName('demande.submit');
    $app->post('/soumettre-dossier','DemandeController:postTestSoumettre');

    //admin principal views
    $app->get('/admin/dashboard','AdminController:getHello')->setName('admin.dashboard');
    $app->get('/admin/users','AdminController:getUsers')->setName('admin.users');
    //admin CRUD
    $app->get('/dossier/{type}/{id}','AdminController:getDossierSoumis')->setName('admin.dossier');
    $app->get('/delete[/{key}/{id}]','AdminController:getDelete')->setName('demande.delete');
    $app->post('/valider','AdminController:postDossierSoumis')->setName('admin.posttest');
    $app->post('/accuser','AdminController:postAccuseReception')->setName('admin.postreception');
    $app->post('/protection','AdminController:postAccuseProtection')->setName('admin.postprotection');
    $app->post('/paiement','AdminController:postAccusePaiement')->setName('admin.postpaiement');
    $app->post('/cloture/{id}','AdminController:postAccuseCloture')->setName('admin.postcloture');

    //test flash
    $app->get('/foo','HomeController:getfoo')->setName('foo');
    $app->get('/bar','HomeController:getbar')->setName('bar');
