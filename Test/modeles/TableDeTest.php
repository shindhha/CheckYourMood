<?php

namespace modeles;
require_once 'modeles/Table.php';

class TableDeTest extends Table
{
    protected $tableName = "tests";
    protected array $fillable = ['nomTest', 'description', 'id'];

    public function __construct($id = 0)
    {
        parent::__construct($id);
    }
}