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

    //Change password stuff
    $app->get('/auth/forgot-password','AuthController:getForgetpassword')->setName('auth.forgot-password');
    $app->post('/auth/forgot-password','AuthController:postForgetpassword');
    //to test
    $app->get('/auth/change-password/{selector}','AuthController:getchangepassword')->setName('auth.change-password');
    $app->post('/auth/change-password/{selector}','AuthController:postchangepassword');

    // //Testing stuff
    $app->get('/test2/{selector}','DemandeController:getTest2')->setName('demande.test2');
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
    $app->post('/delete[/{key}/{id}]','AdminController:getDelete')->setName('demande.delete');
    $app->get('/notification/delete/{id}','AdminController:PushNotifications')->setName('demande.notification');
    $app->post('/valider','AdminController:postDossierSoumis')->setName('admin.posttest');
    $app->post('/accuser','AdminController:postAccuseReception')->setName('admin.postreception');
    $app->post('/protection','AdminController:postAccuseProtection')->setName('admin.postprotection');
    $app->post('/paiement','AdminController:postAccusePaiement')->setName('admin.postpaiement');
    $app->post('/cloture/{id}','AdminController:postAccuseCloture')->setName('admin.postcloture');

    //admin log
    $app->get('/admin/logs','AdminController:getLogs')->setName('admin.logs');
    $app->get('/admin/log/{id}','AdminController:getLog')->setName('admin.log');


    //test flash
    $app->get('/foo','HomeController:getfoo')->setName('foo');
    $app->get('/bar','HomeController:getbar')->setName('bar');

    //View files
    $app->get('/file/{id}/{filename}','DemandeController:getFile')->setName('file');
