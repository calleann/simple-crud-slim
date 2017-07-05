<?php

  namespace Test\Model;

  use Illuminate\Database\Eloquent\Model;

  /**
   *
   */
  class dossier_construire extends Model
  {
    protected $table = 'dossier_construire';

    protected $fillable =[
      'column_construire',
      'demande_autorisation',
      'propriete',
      'plan_topographique',
      'maitrise_oeuvre',
      'plan_structure',
      'note_pressentation',
      'architecte',
      'fiche_technique',
      'bureau_controle',
      'notice_securite',
      'contrat',
      'frais_autorisation',
      'fiche_sapeurs'
    ];

    public function dossier()
    {
      return $this->morphOne('Test\Model\Dossier','dossierable');
    }

    public function protection_civile()
    {
      return $this->hasOne('Test\Model\Protection_civile','dossier_id');
    }

  }
