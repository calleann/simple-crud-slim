<?php

  namespace Test\Model;

  use Illuminate\Database\Eloquent\Model;

  /**
   *
   */
  class User extends Model
  {

    protected $fillable =[
      'name',
      'email',
      'num_tel',
      'cin',
      'password',
      'type'
    ];

    public function dossier()
    {
      return $this->hasOne('Test\Model\Dossier');
    }
  }
