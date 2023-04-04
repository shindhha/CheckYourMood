<?php

namespace Modeles;
require_once 'modeles/Table.php';
class Humeur extends Table
{
    protected $tableName = "humeur";
    protected array $fillable = ['libelle','dateHumeur','heure','idUtil','contexte'];
    protected $primaryKey = "codeHumeur";

    public function __construct($id = 0)
    {
        parent::__construct($id);
    }
}