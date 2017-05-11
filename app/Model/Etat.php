<?php

  namespace Test\Model;

  use Illuminate\Database\Eloquent\Model;

  /**
   *
   */
  class Etat extends Model{

    protected $table = 'etats';

    protected $fillable =[
        'dossier_id',
        'etat1',
        'etat2',
    ];

    public $timestamps = false;

  }
