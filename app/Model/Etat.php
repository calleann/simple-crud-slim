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
        'valide',
        'accuse',
        'protection_civile',
        'paiement',
        'cloture'
    ];

    public $timestamps = false;

  }
