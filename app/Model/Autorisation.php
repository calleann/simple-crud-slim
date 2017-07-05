<?php

  namespace Test\Model;

  use Illuminate\Database\Eloquent\Model;


  class Autorisation extends Model
  {
    protected $table = 'autorisations';

    protected $fillable = [
      'dossier_id',
      'lot',
      'date_commencement'
    ];

  }
