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
      'societe',
      'email',
      'num_tel',
      'cin',
      'password',
      'type',
      'num_decision',
      'date_decision',
      'activite'
    ];

    public function dossier()
    {
      return $this->hasOne('Test\Model\Dossier');
    }
    public function notifications()
    {
      return $this->hasMany('Test\Model\Notification');
    }
  }
