<?php

  namespace Test\Models;

  use Illuminate\Database\Eloquent\Model;

  /**
   *
   */
  class Demande extends Model
  {

    protected $fillable =[
      'nom',
      'details',
      'statut',
      'id_user'
    ];
  }
