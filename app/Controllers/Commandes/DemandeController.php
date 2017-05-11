<?php
  namespace Test\Controllers\Commandes;
  use Test\Controllers\Controller;
  use Test\Model\User;
  use Test\Model\Demande;
  use Test\Model\Dossier;
  use Test\Model\Etat;
  use Test\Model\dossier_construire;
  use Test\Model\dossier_exploitation;
  use Respect\Validation\Validator as v;

  /**
   *
   */
  class DemandeController extends Controller
  {
    public function getTest($request,$response)
    {
      $user = User::find($_SESSION['id']);
      // $dossiers = Dossier::all();
      // foreach($dossiers as $dossier){
      //   if($dossier->dossierable instanceof dossier_exploitation){
      //       echo "exploitation <br>";
      //   }
      //   if($dossier->dossierable instanceof dossier_construire){
      //       echo "construire <br>";
      //   }
      // }
      $dossier = $user->dossier;
      echo $dossier->etat;
      echo "ok";

    }
    public function getTest2($request,$response)
    {
      $dossier = Dossier::find($request->getAttribute('id'));
      echo $dossier->compter();
      echo json_encode($dossier)."<br>";
      echo json_encode($dossier->user)."<br>";
      echo json_encode($dossier->dossierable);

    }
    //
    // public function getAllDemandes($request,$response)
    // {
    //   $demandes = Demande::all();
    //
    //   foreach ($demandes as $demande) {
    //     $user = User::find($demande['id_user']);
    //     $demande['username'] = $user['name'];
    //   }
    //   $env = $this->view->getEnvironment();
    //   $env->addGlobal('demandes',$demandes);
    //   return $this->view->render($response,'cmd/test.twig');
    // }
    //
    // public function getDemandesByUser($request,$response)
    // {
    //   //  $id = (int) $request->getAttribute('id');
    //   $demandes = Demande::where('id_user',$request->getAttribute('id'))->get();
    //   foreach($demandes as $demande){
    //     echo $demande['id_demande']." : ".$demande['nom']." , ".$demande['statut']."<br/>";
    //   }
    //   die();
    // }
    // public function getDemandeDetails($request,$response)
    // {
    //   $demande = Demande::find($request->getAttribute('id'));
    //   // var_dump($demande);
    //   echo $demande['id']." : ".$demande['nom']." , ".$demande['statut']." ,".$demande['details']."<br/>";
    //   die();
    // }
    //
    // public function getAddDemande($request,$response)
    // {
    //
    // }
    // public function postAddDemande($request,$response)
    // {
    //   //Validation
    //         // $validation = $this->validator->validate($request,[
    //         //   'nom'=> v::noWhitespace()->notEmpty(),
    //         //   'details'=>v::notEmpty()->alpha(),
    //         // ]);
    //   //Checking the values
    //         // if($validation->failed()){
    //         //   return $response->withRedirect($this->router->pathFor('auth.signup'));
    //         // }
    //   //creating+rendering
    //         // Demande::create([
    //         //   'nom'=>'',
    //         //   'details'=>'',
    //         //   'statut'=>'',
    //         //   'id_user'=>''
    //         // ]);
    // }
    //
    // public function getUpdateDetail($request,$response)
    // {
    //   // $demande = Demande::find($request->getAttribute('id'));
    //   die();
    // }
    public function getTestform($request,$response)
    {
      // $demande = Demande::where('Num_dossier',$_SESSION['id'])->first();
      if($_SESSION['id'])
      {
        $user = User::find($_SESSION['id']);
        $dossier = $user->dossier;
        $count = $dossier->compter();
        $dossier_fils = $dossier->dossierable;
        $env = $this->view->getEnvironment();
        // $env->addGlobal('demande',$demande);
        $env->addGlobal('dossier_fils',$dossier_fils);
        $env->addGlobal('count',$count);
        $env->addGlobal('statut',$dossier->statut);
        if($_SESSION['type']=== "exploitation")
          return $this->view->render($response,'cmd/test.html.twig');
        if($_SESSION['type']=== "construire")
          return $this->view->render($response,'cmd/test_construire.html.twig');
      }
      return $response->withRedirect($this->router->pathFor('home'));

    }
    public function postTestform($request,$response)
    {
      var_dump($_SESSION);
      echo "<br>";
      $dir = __DIR__.'/../../../dossiers/'.$_SESSION['id'];
      // $demande = Demande::where('Num_dossier',$_SESSION['id'])->first();
      $user = User::find($_SESSION['id']);
      $dossier = $user->dossier;
      $dossier_fils = $dossier->dossierable;
      $files = $request->getUploadedFiles();
      $count = 0;
      echo "<br>";
      // for ($i=0; $i < count($files['test']) ; $i++) {
      //   $file_buffer = $files['test'][$i];
      //   $filename = $file_buffer->getClientFilename();
      //   $extension_upload = strtolower(substr(strrchr($filename, '.'),1)) ;
      //   echo $filename." /extension : ".$extension_upload."<br>";
      //   $file_buffer->moveTo($dir.'/'.utf8_decode($filename));
      //   echo "ok".$i."<br>";
      // }
      $error = null;
      foreach ($files['test'] as $key => $value) {
        echo $key."<br>";
        // echo $demande[$key]."<br>";
        if ($value->getClientFilename()=="") {
          if (empty($dossier_fils[$key])) {
            $error[$key] = "ping error ".$key;
          }
          else {
            echo "that's okay everything is fine bruh! <br>";
          }

        }
        else{
          $filename=$value->getClientFilename();
          $extension_upload = strtolower(substr(strrchr($filename, '.'),1)) ;
          $newFilename = $key.'_'.$_SESSION['id'].'.'.$extension_upload;
          // echo $value->getSize(); don't forget to check the size
          $value->moveTo($dir.'/'.$newFilename);
          // $demande[$key] = 1;
          $dossier_fils[$key] = $newFilename;
          $dossier_fils->save();
          echo "ok";
        }
        echo "<br>";
      }
      // $demande->save();
      // foreach (array_slice($demande['attributes'],1) as $key => $value) {
      //   if( $value == 1 ) $count++;
      // }
      // echo $count;
      $_SESSION['error'] = $error;
      return $response->withRedirect($this->router->pathFor('demande.test'));
      // die();
    }

    public function getTestSoumettre($request,$response){
      if($_SESSION['id'])
      {
        $user = User::find($_SESSION['id']);
        $dossier = $user->dossier;
        $count = $dossier->compter();
        $env = $this->view->getEnvironment();
        $env->addGlobal('statut',$dossier->statut);
        $env->addGlobal('count',$count);
        $env->addGlobal('etat',$dossier->etat);
        return $this->view->render($response,'cmd/test-submit.html.twig');
      }
      return $response->withRedirect($this->router->pathFor('home'));
    }

    public function postTestSoumettre($request,$response){
      $user = User::find($_SESSION['id']);
      $dossier = $user->dossier;
      $dossier->statut = "soumis";
      $dossier->save();
      if($dossier->etat === null)
      {
        $etat = new Etat();
        $dossier->etat()->save($etat);
      }
      return $response->withRedirect($this->router->pathFor('demande.submit'));
    }

  }
//{{ path_for('item', {'slug': item.slug}) }}
