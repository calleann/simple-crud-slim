<?php

  namespace Test\Model;

  use Illuminate\Database\Eloquent\Model;


  /**
   *
   */
  class tracabilite extends Model
  {
    protected $table = "tracabilite";

    protected $fillable = [
      'user_id',
      'dossier_id',
      'update_statut',
      'log',
    ];

    public function user(){
      return $this->belongsTo('Test\Model\User');
    }

    public function dossier(){
      return $this->belongsTo('Test\Model\Dossier');

    }

    public function write_log($statut,$log){
        $this->update_statut = $statut;
        $this->log = $log;
    }

    public function save_entities($user,$dossier){
        $this->user()->associate($user);
        $this->dossier()->associate($dossier);
    }
  }
