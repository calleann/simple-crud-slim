<?php

  namespace Test\Model;

  use Illuminate\Database\Eloquent\Model;

  /**
   *
   */
  class Dossier extends Model
  {
    protected $table = 'dossiers';

    protected $fillable =[
      'user_id',
      'statut',
      'dossierable_id',
      'dossierable_type'
    ];

    public function user()
    {
      return $this->belongsTo('Test\Model\User');
    }
    public function dossierable()
    {
      return $this->morphTo();
    }
    public function etat()
    {
      return $this->hasOne('Test\Model\Etat');
    }

    public function compter(){
      $dossier_fils = $this->dossierable;
      $count = 0;
      foreach (array_slice($dossier_fils['attributes'],1) as $key => $value) {
        if(empty($value)) $count++;
      }
      return $count;
    }

  }
