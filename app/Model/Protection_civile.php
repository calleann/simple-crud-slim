<?php
  namespace Test\Model;

  use Illuminate\Database\Eloquent\Model;


  class Protection_civile extends Model
  {
    protected $table = "protection_civile";

    protected $fillable = [
      'avis',
      'nom_fichier',
      'note',
    ];


  }
