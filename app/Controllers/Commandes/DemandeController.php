<?php
  namespace Test\Controllers\Commandes;
  use Test\Controllers\Controller;
  use Test\Models\User;
  use Test\Models\Demande;
  use Respect\Validation\Validator as v;

  /**
   *
   */
  class DemandeController extends Controller
  {
    public function getTest($request,$response)
    {
      var_dump($request->getAttribute('id'));
      die();
    }

    public function getAllDemandes($request,$response)
    {
      $demandes = Demande::all();

      foreach ($demandes as $demande) {
        $user = User::find($demande['id_user']);
        $demande['username'] = $user['name'];
      }
      $env = $this->view->getEnvironment();
      $env->addGlobal('demandes',$demandes);
      return $this->view->render($response,'cmd/test.twig');
    }

    public function getDemandesByUser($request,$response)
    {
      //  $id = (int) $request->getAttribute('id');
      $demandes = Demande::where('id_user',$request->getAttribute('id'))->get();
      foreach($demandes as $demande){
        echo $demande['id_demande']." : ".$demande['nom']." , ".$demande['statut']."<br/>";
      }
      die();
    }
    public function getDemandeDetails($request,$response)
    {
      $demande = Demande::find($request->getAttribute('id'));
      // var_dump($demande);
      echo $demande['id']." : ".$demande['nom']." , ".$demande['statut']." ,".$demande['details']."<br/>";
      die();
    }

    public function getAddDemande($request,$response)
    {
      
    }
    public function postAddDemande($request,$response)
    {
      //Validation
            // $validation = $this->validator->validate($request,[
            //   'nom'=> v::noWhitespace()->notEmpty(),
            //   'details'=>v::notEmpty()->alpha(),
            // ]);
      //Checking the values
            // if($validation->failed()){
            //   return $response->withRedirect($this->router->pathFor('auth.signup'));
            // }
      //creating+rendering
            // Demande::create([
            //   'nom'=>'',
            //   'details'=>'',
            //   'statut'=>'',
            //   'id_user'=>''
            // ]);
    }

    public function getUpdateDetail($request,$response)
    {
      // $demande = Demande::find($request->getAttribute('id'));
      die();
    }

  }
//{{ path_for('item', {'slug': item.slug}) }}
