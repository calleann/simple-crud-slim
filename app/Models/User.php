<?php

  namespace Test\Models;

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
  }
