<?php
  namespace Test\Model;

  use Illuminate\Database\Eloquent\Model;

  class Notification extends Model
  {
    protected $table = 'notifications';

    protected $fillable = [
      'user_id',
      'dossier_statut',
      'type',
      'message'
    ];

  }
