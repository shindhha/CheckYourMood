<?php

namespace Modeles;
require_once 'Table.php';
class User extends Table
{
    protected $tableName = "utilisateur";
    protected array $fillable = ['prenom','nom','identifiant','mail','motDePasse'];
    protected $primaryKey = "codeUtil";

    public function __construct($id = 0)
    {
        parent::__construct($id);
    }
}