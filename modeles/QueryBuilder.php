<?php

namespace Modeles;
use PDOStatement;
use UnexpectedValueException;
use PDO;
class QueryBuilder
{
    // La requete construite
    private mixed $query;
    // Objet PDO établissant la connexion a la base de données
    private static ?PDO $pdo;
    // Champ souhaiter lors d'une requete select
    private array $fields;
    // Parametre généraux de la requete
    private array $params;
    // Les condition sont stocker sous forme de string
    private array $whereClose;
    // Nom de la table cible
    private string $tableName;

    private function __construct()
    {
        $this->query = null;
        $this->fields = [];
        $this->params = [];
        $this->whereClose = [];
    }

    /**
     * Substitue du constructeur par défaut , plus explicite car on comprend que
     * l'on doit renseigne un nom de table en argument.
     * Permet également d'écrire les requetes d'une seule traite.
     * @param string $tableName nom de la table par defaut dans laquelle faire la requete
     * @return QueryBuilder le nouveau constructeur de requete sql
     */
    public static function Table(string $tableName): QueryBuilder
    {
        $q = new QueryBuilder();
        $q->tableName = $tableName;
        return $q;
    }

    /**
     * Permet de changer le lien vers la base de données
     * @param $pdo l'objet pdo du lien vers la base de données
     */
    public static function setDBSource($pdo): void
    {
        static::$pdo = $pdo;
    }

    /**
     * Permet de spécifier les colonnes que l'on souhaite récupérer dans notre requete
     * que sa soit de la forme d'un tableau ou une série d'arguement
     *               (['nom','prenom',...]) ou ('nom','prenom',...)
     * @param ...$params Liste des colonnes sous forme d'argument
     * @return $this la requete pour continuer la construction en chaine
     */
    public function select(...$params): QueryBuilder
    {
        foreach ($params as $param) {
            $this->fields[] = $param;
        }
        return $this;
    }

    /**
     * Permet d'ajouter une condition a la requete sql.
     * @param string $columnName Colonne concerné par la condition.
     * @param string $operator Indique comment la colonne doit matcher la valeur.
     * @param $value Valeur que doit matcher la colonne en fonction de l'operateur.
     * @return $this la requete pour continuer la construction en chaine
     */
    public function where(string $columnName, string $operator = '=', mixed $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = "=";
        }
        $this->whereClose[] = $columnName . " " . $operator . " :" . $columnName;
        $this->params[$columnName] = $value;
        return $this;
    }

    /**
     * Permet de construire une requete update en spécifiant les colonnes concerné dans $column
     * @param array $column Liste des colonnes dont la valeur doit changer.
     *                Les valeurs sont également spécifier sous cette forme [column => valeur]
     * @return QueryBuilder le constructeur de requete courant
     */
    public function update(array $column): QueryBuilder
    {
        $params = "";
        foreach ($column as $keys => $value) {
            if ($value != null) {
                $this->params[$keys] = $value;
                if ($params != "") $params .= ",";
                $params .= $keys . " = :" . $keys;
            }
        }
        $this->query =  "UPDATE " . $this->tableName . " SET " . $params . $this->getWhereClose();
        return $this;
    }

    /**
     * Execute la requete de type select précédement construite puis renvoie le pdo statement
     * @return string Une requete sql de type select
     */
    private function construct_select_request(): string
    {
        $select = "";
        if (empty($this->fields)) {
            $select = "*";
        } else {
            foreach ($this->fields as $column) {
                if ($select != "") {
                    $select .= ",";
                }
                $select .= $column;
            }
        }
        return "SELECT " . $select . " FROM " . $this->tableName . $this->getWhereClose();
    }

    /**
     * Récupére les conditions stocker sous forme de chaine de caractère
     * ensuite utilise dans la construction des requete.
     * @return string conditions sous chaine de caractère
     */
    private function getWhereClose(): string
    {
        $closes = "";
        foreach ($this->whereClose as $close) {
            if ($closes == "") {
                $closes = " WHERE ";
            } else {
                $closes .= " AND ";
            }
            $closes .= $close;
        }
        return $closes;
    }

    /**
     * Construit la requete sql permetant d'insert une ligne dans la base de données
     * @param array $column Le tableau des champs a insérer ['colonne' => 'valeur']
     * @return QueryBuilder Le constructeur de requete courant
     */
    public function insert(array $column): QueryBuilder
    {
        $fields = "";
        $params = "";
        foreach ($column as $keys => $value) {
            if ($fields != "") {
                $fields .= ",";
                $params .= ",";
            }
            $fields .= $keys;
            $params .= ":" . $keys;
            $this->params[$keys] = $value;
        }
        $this->query = "INSERT INTO " . $this->tableName . " (". $fields . ") VALUES " . "(" . $params . ")";
        return $this;
    }
    /**
     * Prepare et Execute la requete avec ses arguments par l'objet pdo
     * @return PDOStatement Le statement résultant de l'éxécution par l'objet pdo
     */
    public function execute(): \PDOStatement
    {
        if ($this->query == null) {
            $this->query = $this->construct_select_request();
        }
        if (static::$pdo == null) {
            throw new UnexpectedValueException();
        }
        $stmt = static::$pdo->prepare($this->query);
        $stmt->execute($this->params);
        return $stmt;
    }
    public function getQuery() {
        if ($this->query == null) {
            $this->query = $this->construct_select_request();
        }
        return $this->query;
    }

    public function getParams() {
        return $this->params;
    }

}