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
        $dossier = Dossier::find($request->getParam('id'));
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
          }
          else
          {
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
          }
          $dossier->dossierable->protection_civile()->save($protection_civile);
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
          }
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
      $pdf->setBodyLine($dossier->user->id,$dossier->user->societe,date('d/m/Y',$date_commencement),date("d/m/Y"),$request->getParam('lot'));
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
        //redirect
        $response = $this->response->withHeader( 'Content-type', 'application/pdf' );
        $response->write( $pdf->Output('My cool PDF', 'S',true));
        return $response;
      }


    }

    public function getDelete($request,$response)
    {
      //alimenter une table de notifications
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
      $notification->message = "le fichier :".$key." a ete rejete";
      $user = $dossier->user;
      $user->notifications()->save($notification);

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
  }
