<?php
  namespace Test\Controllers\Commandes;
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
  /**
   *
   */
  class DemandeController extends Controller
  {
    public function getFile($request,$response)
    {
      $id = $request->getAttribute('id');

      if(isset($_SESSION['id']) && isset($_SESSION['type']) && $_SESSION['type'] == "admin" || $id  == $_SESSION['id']){
        $filename = $request->getAttribute('filename');
        echo $filename;
        $file =  __DIR__.'/../../../dossiers/'.$id.'/'.$filename;
        if(file_exists($file))
        {
          $response = $this->response->withHeader( 'Content-type', 'application/pdf' );
          readfile($file);
          return $response;
        }
        return $this->view->render($response,'templates/404.html.twig');
      }
      else{
        return $this->view->render($response,'templates/405.html.twig');
      }

    }
    public function getTest($request,$response)
    {


    }
    public function getTest2($request,$response)
    {
      $user = User::where('selector',$request->getAttribute('selector'))->first();
      echo json_encode($user);
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
            $error[$key] = 1;
          }
          else {
            echo "that's okay everything is fine bruh! <br>";
          }

        }
        else{
          $filename=$value->getClientFilename();
          $extension_upload = strtolower(substr(strrchr($filename, '.'),1));
          if($extension_upload != "pdf"){
            $error[$key] = 2;
            continue;
          }
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
        $env->addGlobal('user',$user);
        $env->addGlobal('etat',$dossier->etat);
        return $this->view->render($response,'cmd/test-submit.html.twig');
      }
      return $response->withRedirect($this->router->pathFor('home'));
    }

    public function postTestSoumettre($request,$response){
      $user = User::find($_SESSION['id']);
      $notifications = Notification::where('user_id',$user->id)->delete();
      $dossier = $user->dossier;
      $dossier->statut = 1;
      $dossier->save();
      return $response->withRedirect($this->router->pathFor('demande.submit'));
    }

    public function getUserInfo($request,$response)
    {
      if($_SESSION['id'])
      {
        $user = User::find($_SESSION['id']);
        $env = $this->view->getEnvironment();
        $env->addGlobal('num_decision',$user->num_decision);
        $env->addGlobal('date_decision',$user->date_decision);
        $env->addGlobal('activite',$user->activite);
        return $this->view->render($response,'cmd/info-sup.html.twig');
      }
      return $response->withRedirect($this->router->pathFor('home'));
    }

    public function postUserInfo($request,$response)
    {
      $user = User::find($_SESSION['id']);
      $validation = $this->validator->validate($request,[
        'num_decision'=> v::noWhitespace()->notEmpty()->Digit(),
        'date_decision'=> v::date(),
        'activite' => v::notEmpty()
      ]);
      if($validation->failed()){
        return $response->withRedirect($this->router->pathFor('demande.user'));
      }
      $date_decision = strtotime($request->getParam('date_decision'));
      $user->num_decision = $request->getParam('num_decision');
      $user->date_decision = date('Y-m-d',$date_decision);
      $user->activite = $request->getParam('activite');
      $user->save();
      echo 'yay';
    }

  }
//{{ path_for('item', {'slug': item.slug}) }}
