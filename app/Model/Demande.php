<?php

  namespace Test\Model;

  use Illuminate\Database\Eloquent\Model;

  /**
   *
   */
  class Demande extends Model
  {

    protected $fillable =[
      'Num_dossier',
      'statut',
      'demande_ecrite',
      'architecte',
      'bureau_controle',
      'assurance',
      'protection_civile',
      'reseau',
      'recollement',
      'fin_suivi'
    ];
  }
