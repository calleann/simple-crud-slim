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
      foreach($demandes as $demande){
        echo $demande['id_demande']." : ".$demande['nom']." , ".$demande['statut']." , ".$demande['username']."<br/>";
      }
      die();
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
      $demande = Demande::where('id_demande',$request->getAttribute('id'))->first();
      // var_dump($demande);
      echo $demande['id_demande']." : ".$demande['nom']." , ".$demande['statut']." ,".$demande['details']."<br/>";
      die();
    }
  }
//{{ path_for('item', {'slug': item.slug}) }}
