<?php
  namespace Test\Controllers\Admin;
  use Test\Controllers\Controller;
  use Test\Model\User;
  use Test\Model\Demande;
  use Test\Model\Dossier;
  use Test\Model\Etat;
  use Test\Model\dossier_construire;
  use Test\Model\dossier_exploitation;
  use Test\Model\Protection_civile;
  use Test\Model\PDF;
  use Test\Model\Autorisation;
  use Test\Model\Notification;
  use Test\Model\tracabilite;
  use Respect\Validation\Validator as v;


  class AdminController extends Controller
  {


    public function getHello($request,$response)
    {
      if(isset($_SESSION['type']) && $_SESSION['type'] === "admin")
      {
        // echo 'hello admin';
        $dossiers_soumis = Dossier::where('statut',1)->get();
        $dossiers_nouveau = Dossier::where('statut',0)->get();
        $dossiers_valide = Dossier::where('statut',2)->get();
        $dossiers_accuse = Dossier::where('statut',3)->get();
        $dossiers_protection = Dossier::where('statut',4)->get();
        $dossiers_paiement = Dossier::where('statut',5)->get();
        $dossiers_cloture = Dossier::where('statut',6)->get();
        $env = $this->view->getEnvironment();
        $env->addGlobal('dossiers_soumis',$dossiers_soumis);
        $env->addGlobal('dossiers_nouveau',$dossiers_nouveau);
        $env->addGlobal('dossiers_valide',$dossiers_valide);
        $env->addGlobal('dossiers_accuse',$dossiers_accuse);
        $env->addGlobal('dossiers_protection',$dossiers_protection);
        $env->addGlobal('dossiers_paiement',$dossiers_paiement);
        $env->addGlobal('dossiers_cloture',$dossiers_cloture);
        return $this->view->render($response,'admin/admin-page.html.twig');
      }
      else
      {
        return $response->withRedirect($this->router->pathFor('home'));
      }
    }

    public function getDossierSoumis($request,$response)
    {
      if(isset($_SESSION['type']) && $_SESSION['type'] === "admin")
      {
        $messages = $this->flash->getMessages();
        $dossier = Dossier::find($request->getAttribute('id'));
        // echo "user info <br>";
        // echo json_encode($dossier->user);
        // echo "<br>dossier <br>";
        // echo json_encode($dossier);
        // echo "<br>les documents<br>";
        // echo json_encode($dossier->dossierable);
        $env = $this->view->getEnvironment();
        $env->addGlobal('dossier',$dossier);
        if(!empty($messages['test'])){
          $env->addGlobal('message',"shit is deleted");
        }
        if(!empty($messages['erreur'])){
          // var_dump($messages['erreur']);
          // die();
          $env->addGlobal('erreur',$messages['erreur'][0]);
        }
        if(!empty($messages['valide'])){
          $env->addGlobal('valide',$messages['valide'][0]);
        }
        return $this->view->render($response,'admin/dossier-soumis.html.twig');
      }
      else
      {
        return $response->withRedirect($this->router->pathFor('home'));
      }
    }

    public function postDossierSoumis($request,$response)
    {
      if(isset($_SESSION['type']) && $_SESSION['type'] === "admin")
      {
        $dossier = Dossier::find($request->getParam('id'));


        $admin = User::find($_SESSION['id']);

        //Email notification
        $this->mail->addAddress('anas.riyuu@gmail.com','chafik anasse');//switch it back to the mail address
        $this->mail->Subject  = utf8_decode("Dossier N:".$dossier->user->id." Validation du dossier");
        $this->mail->Body     = utf8_decode("Votre dossier a été validé par l'administration de la Zone Franche.Veuillez se connecter sur la plateforme pour se renseigner sur les étapes du traitement");


        if(!$this->mail->send()) {
          $this->flash->addMessage('erreur',"Erreur d'envoi de mail de notification");
          echo 'Message was not sent.';
          echo 'Mailer error: ' . $this->mail->ErrorInfo;
        } else {
          $this->flash->addMessage('valide',utf8_decode("Message envoyé"));
          echo 'Message has been sent.';
          $dossier->statut = 2;
          $dossier->save();

          if($dossier->etat === null)
          {
            $etat = Etat::create([
              'valide' => 1
            ]);
            $dossier->etat()->save($etat);
          }
          else {
            $etat = $dossier->etat;
            $etat->valide = 1;
            $etat->save();
          }
          //notifications
          $delete_notification = Notification::where('user_id',$dossier->user->id)->delete();
          $notification = new notification();
          $notification->dossier_statut = $dossier->statut;
          $notification->type = "success";
          $notification->message = "Votre dossier a ete valide par l'administrateur";
          $user = $dossier->user;
          $user->notifications()->save($notification);
          //log
          $msg = "Validation des documents";
          $trac = new tracabilite;
          $trac->write_log($dossier->statut,$msg);
          $trac->save_entities($admin,$dossier);
          $trac->save();
        }

        return $response->withRedirect($this->router->pathFor('admin.dossier',array('id'=>$dossier->id,'type'=>$dossier->user->type)));
        echo $request->getParam('id');
        echo "ok";
      }
      else
      {
        return $response->withRedirect($this->router->pathFor('home'));
      }
    }
    public function postAccuseReception($request,$response)
    {
      if(isset($_SESSION['type']) && $_SESSION['type'] === "admin")
      {
        $admin = User::find($_SESSION['id']);
        $dossier = Dossier::find($request->getParam('id'));
        //Email notification
        $this->mail->addAddress('anas.riyuu@gmail.com','chafik anasse');//switch it back to the mail address
        $this->mail->Subject  = utf8_decode("Dossier N:".$dossier->user->id." Accusé de réception des documents");
        $this->mail->Body     = utf8_decode("Nous avons bien reçus vos documents,la protection civile traitera votre demande.Vous receverez une réponse dans la plateforme à l'égard de la décision prise.");


        if(!$this->mail->send()) {
          $this->flash->addMessage('erreur',"Erreur d'envoi de mail de notification");
          echo 'Message was not sent.';
          echo 'Mailer error: ' . $this->mail->ErrorInfo;
        } else {
          $this->flash->addMessage('valide',utf8_decode("Message envoyé"));
          echo 'Message has been sent.';

          $dossier->statut = 3;
          $etat = $dossier->etat;
          $etat->accuse = 1;
          $etat->save();
          $dossier->save();
          //notifications
          $delete_notification = Notification::where('user_id',$dossier->user->id)->delete();
          $notification = new notification();
          $notification->dossier_statut = $dossier->statut;
          $notification->type = "success";
          $notification->message = "Nous avons accuse les fichiers necessaires";
          $user = $dossier->user;
          $user->notifications()->save($notification);

          //log
          $msg = "Accusé de la réception des documents auprès de ".$user->name;
          $trac = new tracabilite;
          $trac->write_log($dossier->statut,$msg);
          $trac->save_entities($admin,$dossier);
          $trac->save();
        }


        return $response->withRedirect($this->router->pathFor('admin.dossier',array('id'=>$dossier->id,'type'=>$dossier->user->type)));
      }
      else
      {
        return $response->withRedirect($this->router->pathFor('home'));
      }
    }

    public function postAccuseProtection($request,$response)
    {
      if(isset($_SESSION['type']) && $_SESSION['type'] === "admin")
      {
        $admin = User::find($_SESSION['id']);
        $dossier = Dossier::find($request->getParam('id'));
        echo json_encode($dossier->dossierable);

        if($dossier->dossierable instanceof dossier_construire)
        {
          echo "<br>";
          echo "ceci est un dossier de construction";
          if($dossier->dossierable->protection_civile === null)
          {
            echo "<br>";
            echo "pas d'avis de protection civile";
            $protection_civile = new Protection_civile();

          }
          else
          {
            $protection_civile = $dossier->dossierable->protection_civile ;
          }
          $protection_civile->avis = $request->getParam('avis');
          $protection_civile->note = $request->getParam('note');
          $dir = __DIR__.'/../../../dossiers/'.$dossier->user->id;
          echo $dir ;
          $files = $request->getUploadedFiles();
          echo $request->getParam('note');
          echo $files['nom_fichier']->getClientFilename();
          if ($files['nom_fichier']->getClientFilename() === null) {
            $error = "nom de fichier vide";
            $_SESSION['error'] = $error;
          }
          else
          {
            $myFile = $files['nom_fichier'];
            if ($myFile->getError() === UPLOAD_ERR_OK) {
              $filename=$myFile->getClientFilename();
              $extension_upload = strtolower(substr(strrchr($filename, '.'),1)) ;
              $newFilename = 'protection_civile_'.$dossier->user->id.'.'.$extension_upload;
              $myFile->moveTo($dir.'/' .$newFilename);
              $protection_civile->nom_fichier = $newFilename;
            }
          }
          if($request->getParam('avis') === "favorable")
          {
            $this->mail->addAddress('anas.riyuu@gmail.com','chafik anasse');//switch it back to the mail address
            $this->mail->Subject  = utf8_decode("Dossier N:".$dossier->user->id." Avis de la protection civile");
            $this->mail->Body     = utf8_decode("La protection civile a accordé un avis favorable à l'égard de votre demande.Veuillez se présenter à l'administration avec les frais d'autorisation ainsi que le reste des documents");

            $dossier->statut = 4;
            $dossier->save();
            $etat = $dossier->etat;
            $etat->protection_civile = 1;
            $etat->save();
            //notification
            $delete_notification = Notification::where('user_id',$dossier->user->id)->delete();
            $notification = new notification();
            $notification->dossier_statut = $dossier->statut;
            $notification->type = "success";
            $notification->message = "Vous avez recu un avis favorable de la part de la protection civile";
            $user = $dossier->user;
            $user->notifications()->save($notification);

            //log
            $msg = "Avis favorable de la protection civile";
            $trac = new tracabilite;
            $trac->write_log($dossier->statut,$msg);
            $trac->save_entities($admin,$dossier);
            $trac->save();

          }
          else
          {
            $this->mail->addAddress('anas.riyuu@gmail.com','chafik anasse');//switch it back to the mail address
            $this->mail->Subject  = utf8_decode("Dossier N:".$dossier->user->id." Avis de la protection civile ");
            $this->mail->Body     = utf8_decode("Votre demande a été rejeté par les services de la protection civile....");

            $dossier->statut = -4;
            $dossier->save();
            //notification
            $delete_notification = Notification::where('user_id',$dossier->user->id)->delete();
            $notification = new notification();
            $notification->dossier_statut = $dossier->statut;
            $notification->type = "danger";
            $notification->message = "Votre dossier a ete rejete par la protection civile";
            $user = $dossier->user;
            $user->notifications()->save($notification);

            //log
            $msg = "Avis défavorable de la protection civile";
            $trac = new tracabilite;
            $trac->write_log($dossier->statut,$msg);
            $trac->save_entities($admin,$dossier);
            $trac->save();
          }
          $dossier->dossierable->protection_civile()->save($protection_civile);
          if(!$this->mail->send()) {
            $this->flash->addMessage('erreur',"Erreur d'envoi de mail de notification");
            echo 'Message was not sent.';
            echo 'Mailer error: ' . $this->mail->ErrorInfo;
          } else {
            $this->flash->addMessage('valide',utf8_decode("Message envoyé"));
            echo 'Message has been sent.';
          }

          echo 'ok';
          return $response->withRedirect($this->router->pathFor('admin.dossier',array('id'=>$dossier->id,'type'=>$dossier->user->type)));
        }
      }
      else
      {
        return $response->withRedirect($this->router->pathFor('home'));
      }
    }

    public function postAccusePaiement($request,$response)
    {
      if(isset($_SESSION['type']) && $_SESSION['type'] === "admin")
      {
        $admin = User::find($_SESSION['id']);
        $dossier = Dossier::find($request->getParam('id'));
        $dir = __DIR__.'/../../../dossiers/'.$dossier->user->id;
        echo $dir ;
        $files = $request->getUploadedFiles();
        if ($files['fichier_paiement']->getClientFilename() === null) {
          $error = "nom de fichier vide";
          $_SESSION['error'] = $error;
        }
        else
        {
          $myFile = $files['fichier_paiement'];
          if ($myFile->getError() === UPLOAD_ERR_OK)
          {
            $filename= $myFile->getClientFilename();
            $extension_upload = strtolower(substr(strrchr($filename, '.'),1)) ;
            $newFilename = 'frais_de_paiement_'.$dossier->user->id.'.'.$extension_upload;
            $myFile->moveTo($dir.'/' .$newFilename);
            echo "ok";
            $dossier->statut = 5;
            $dossier->save();
            $etat = $dossier->etat;
            $etat->paiement = 1;
            $etat->save();
            //notification
            $delete_notification = Notification::where('user_id',$dossier->user->id)->delete();
            $notification = new notification();
            $notification->dossier_statut = $dossier->statut;
            $notification->type = "success";
            $notification->message = "Nous avons bien recu le paiement";
            $user = $dossier->user;
            $user->notifications()->save($notification);

            //log
            $msg = "Accusé de réception des frais d'autorisation";
            $trac = new tracabilite;
            $trac->write_log($dossier->statut,$msg);
            $trac->save_entities($admin,$dossier);
            $trac->save();
          }
        }
        $this->mail->addAddress('anas.riyuu@gmail.com','chafik anasse');//switch it back to the mail address
        $this->mail->Subject  = utf8_decode("Dossier N:".$dossier->user->id." Accusé des frais d'autorisation");
        $this->mail->Body     = utf8_decode("Votre fichier d'autorisation a été généré.");

        if(!$this->mail->send()) {
          $this->flash->addMessage('erreur',"Erreur d'envoi de mail de notification");
          echo 'Message was not sent.';
          echo 'Mailer error: ' . $this->mail->ErrorInfo;
        } else {
          $this->flash->addMessage('valide',utf8_decode("Message envoyé"));
          echo 'Message has been sent.';
        }


        echo json_encode($dossier->user);
        return $response->withRedirect($this->router->pathFor('admin.dossier',array('id'=>$dossier->id,'type'=>$dossier->user->type)));
      }
      else
      {
        return $response->withRedirect($this->router->pathFor('home'));
      }
    }

    public function postAccuseCloture($request,$response)
    {
      $admin = User::find($_SESSION['id']);
      $dossier = Dossier::find($request->getAttribute('id'));
      $validation = $this->validator->validate($request,[
        'lot'=> v::noWhitespace()->notEmpty(),
        'date_commencement'=> v::date()->min('today')
      ]);
      if($validation->failed()){
        return $response->withRedirect($this->router->pathFor('admin.dossier',array('id'=>$dossier->id,'type'=>$dossier->user->type)));
      }
      $date_commencement = strtotime($request->getParam('date_commencement'));
      $autorisation = new Autorisation;
      $autorisation->lot = $request->getParam('lot');
      $autorisation->date_commencement = date('Y-m-d',$date_commencement);
      $dossier->autorisation()->save($autorisation);
      $pdf = new PDF;
      $pdf->AliasNbPages();
      $pdf->setBodyLine($dossier->user->num_decision,$dossier->user->societe,date('d/m/Y',$date_commencement),date("d/m/Y"),$request->getParam('lot'));
      $pdf->CorpsRules();
      if($pdf->savePdflocation($dossier->user->id))
      {
        $etat = $dossier->etat;
        $etat->cloture = 1;
        $etat->save();
        $dossier->statut = 6;
        $dossier->save();
        //notification
        $delete_notification = Notification::where('user_id',$dossier->user->id)->delete();
        $notification = new notification();
        $notification->dossier_statut = $dossier->statut;
        $notification->type = "success";
        $notification->message = "Votre dossier a ete traite avec succes,votre autorisation a ete genere";
        $user = $dossier->user;
        $user->notifications()->save($notification);

        //log
        $msg = "génération d'autorisation de commencement des travaux :".$user->societe;
        $trac = new tracabilite;
        $trac->write_log($dossier->statut,$msg);
        $trac->save_entities($admin,$dossier);
        $trac->save();

        $this->mail->addAddress('anas.riyuu@gmail.com','chafik anasse');//switch it back to the mail address
        $this->mail->Subject  = utf8_decode("Dossier N:".$dossier->user->id." Géneration d'autorisation");
        $this->mail->Body     = utf8_decode("Nous avons accusé les frais d'autorisation concernant votre demande.");

        if(!$this->mail->send()) {
          $this->flash->addMessage('erreur',"Erreur d'envoi de mail de notification");
          echo 'Message was not sent.';
          echo 'Mailer error: ' . $this->mail->ErrorInfo;
        } else {
          $this->flash->addMessage('valide',utf8_decode("Message envoyé"));
          echo 'Message has been sent.';
        }


        //redirect
        $response = $this->response->withHeader( 'Content-type', 'application/pdf' );
        $response->write( $pdf->Output('My cool PDF', 'S',true));
        return $response;
      }


    }

    public function getDelete($request,$response)
    {
      $admin = User::find($_SESSION['id']);
      //alimenter une table de notifications
      $nomclature_construction = $this->nomclature_construction;
      $dossier = Dossier::find($request->getAttribute('id'));
      $documents = $dossier->dossierable;
      $key = $request->getAttribute('key');
      echo $documents[$key]."<br>";
      $documents[$key] = "";
      $documents->save();
      $dossier->statut = 0;
      $dossier->save();
      //notifications
      $notification = new notification();
      $notification->dossier_statut = $dossier->statut;
      $notification->type = "danger";
      $notification->message = "le fichier : ".$nomclature_construction[$key]." a été rejeté en raison de : ".$request->getParam('note');
      $user = $dossier->user;
      $user->notifications()->save($notification);

      //log
      $msg = $notification->message;
      $trac = new tracabilite;
      $trac->write_log($dossier->statut,$msg);
      $trac->save_entities($admin,$dossier);
      $trac->save();


      echo "yay<br>";
      echo json_encode($dossier)."<br>";
      echo json_encode($documents);
      $this->flash->addMessage('test','testing one two');
      return $response->withRedirect($this->router->pathFor('admin.dossier',array('id'=>$dossier->id,'type'=>$dossier->user->type)));

    }
    public function getUsers($request,$response)
    {
      if(isset($_SESSION['type']) && $_SESSION['type'] === "admin")
      {
        // echo 'hello admin';
        $users = User::where('type','<>','admin')->get();
        $env = $this->view->getEnvironment();
        $env->addGlobal('users',$users);
        return $this->view->render($response,'admin/admin-users.html.twig');
      }
      else
      {
        return $response->withRedirect($this->router->pathFor('home'));
      }
    }

    public function PushNotifications($request,$response)
    {
      echo $request->getAttribute('id');
      $dossier = Dossier::find($request->getAttribute('id'));
      $notifications = Notification::where('user_id',$dossier->user->id)->get();
      echo json_encode($notifications);
      echo "<br>";
      $message = "<ul>";
      foreach($notifications as $notification) {
        $message .= "<li>".$notification->message."</li>";
      }
      $message .= "</ul>";
      echo $message;

      $this->mail->addAddress('anas.riyuu@gmail.com','chafik anasse');//switch it back to the mail address
      $this->mail->Subject  = utf8_decode("Dossier N:".$dossier->user->id." documents refusés");
      $this->mail->Body     = $message;
      $this->mail->IsHTML(true);

      if(!$this->mail->send()) {
        $this->flash->addMessage('erreur',"Erreur d'envoi de mail de notification");
        echo 'Message was not sent.';
        echo 'Mailer error: ' . $this->mail->ErrorInfo;
      } else {
        $this->flash->addMessage('valide',utf8_decode("Message envoyé"));
        echo 'Message has been sent.';
      }
      return $response->withRedirect($this->router->pathFor('admin.dossier',array('id'=>$dossier->id,'type'=>$dossier->user->type)));

    }

    public function getLogs($request,$response)
    {
      if (isset($_SESSION['type']) && $_SESSION['type'] === "admin")
      {
        $logs = tracabilite::orderBy('created_at','DESC')->get();
        $env = $this->view->getEnvironment();
        $env->addGlobal('logs',$logs);
        return $this->view->render($response,'admin/logs.html.twig');
      }
      else
      {
        return $response->withRedirect($this->router->pathFor('home'));
      }
    }
    public function getLog($request,$response)
    {
      if (isset($_SESSION['type']) && $_SESSION['type'] === "admin")
      {
        $log = tracabilite::find($request->getAttribute('id'));
        echo json_encode($log);
        echo "yay";
      }
      else
      {
        return $response->withRedirect($this->router->pathFor('home'));
      }
    }
  }
