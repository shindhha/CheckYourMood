<?php

namespace Modeles;


use http\Exception\InvalidArgumentException;
use http\Exception\UnexpectedValueException;

/**
 * Etendre un objet par cette classe permet d'interagir avec la base de données
 * en utilisant la programmation orienté objet.
 * Les attributs de cette classe on pour but d'être override de façon a représenter
 * une table dans la base de données.
 * Par exemple une table 'utilisateurs' avec les colonnes 'pseudo' , 'motDePasse'
 * pourra être représenter donnant la valeur 'utilisateurs' a l'attribut 'tableName'
 * et en donnant un tableau ['pseudo','motDePasse'] à l'attribut 'fillable'.
 */
class Table
{
    protected $tableName;
    protected $fillable;
    protected $primaryKey;
    private $id;

    protected function __construct($id = 0)
    {
        if ($id < 0) {
            throw new InvalidArgumentException("L'id ne peut pas être inférieure à 0");
        }
        $this->id = $id;
    }

    /**
     * Recupère les informations demander depuis la base de données jusqu'à l'objet courant
     * @param ...$column la liste des colonnes souhaiter
     * @throws \UnexpectedValueException
     *         si l'id de l'objet est égal a 0 puisque l'id minimal dans notre base de données
     *         vaut 1
     */
    public function fetch(...$column) {
        if ($this->id == 0) {
           throw new UnexpectedValueException("Aucune ligne ne peut avoir l'id 0 !");
        }
        $dataValues = Queries::Table($this->tableName)
            ->select($column)
            ->where($this->primaryKey,$this->id)
            ->execute()->fetch();
        // puis on initialise les attributs de l'objet aux valeurs de la base
        foreach ($dataValues as $keys => $value) {
            $this->$keys = $value;
        }
    }

    /**
     * Associe les attributs de l'objet lister dans 'fillable'
     * avec les valeurs passé en argument.
     * Si un attribut n'est pas lister dans values alors on assignera
     * la valeur null a l'attribut.
     * @param values Les valeurs a associer aux attributs de l'objet
     *               sous forme d'un tableau ['clef' => 'valeur' , ...]
     */
    public function fill($values) {
        foreach ($this->fillable as $keys) {
            $this->$keys = $values[$keys];
        }
    }
    /**
     * Associe les attributs null de l'objet lister dans 'fillable'
     * avec les valeurs passé en argument.
     * @param values Les valeurs a associer aux attributs de l'objet
     *               sous forme d'un tableau ['clef' => 'valeur' , ...]
     */
    public function fillWithoutOverride($values) {
        foreach ($this->fillable as $keys) {
            if ($this->$keys == null) {
                $this->$keys = $values[$keys];
            }
        }
    }

    /**
     * Empaquete les attributs de l'objet lister dans 'fillable'
     * à l'intérieur d'un tableau sous la forme ['nomAttribut' => 'valeur']
     */
    public function toArray() {
        $fields = [];
        foreach ($this->fillable as $key) {
            if ($this->$key != null) {
                $fields[$key] = $this->$key;
            }
        }
        return $fields;
    }

    /**
     * Si l'id de l'objet est égale a 0 :
     * Insère les attributs de l'objet dans la table
     * 'tableName' de la base de données puis récupère l'id
     * Sinon update les valeurs des attributs de l'objet pour la ligne avec l'id 'id'
     */
    public function save() {
        $q = QueryBuilder::Table($this->tableName);
        if ($this->id == 0) {
            $this->id = $q->insert($this->toArray());
        } else {
            $queries = $q->where($this->primaryKey,$this->id)
                       ->update($this->toArray());
        }
    }

    /**
     * Assigne null a tout les attributs lister dans fillable
     */
    public function reset() {
        $this->fill([]);
    }

    public function getPrimaryKey() {
        return $this->primaryKey;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getTableName() {
        return $this->tableName;
    }
}