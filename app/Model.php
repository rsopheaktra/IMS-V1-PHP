<?php
abstract class Model{
    // Informations de la base de données
    private $host = "0.0.0.0";
    private $db_name = "daly_test";
    private $username = "root";
    private $password = "root";
/* 
    private $host = "localhost";
    private $db_name = "ct_huynh_cliknfind";
    private $username = "ct-huynh";
    private $password = "fxdMNbTiCj9sjsG";
*/
    // Propriété qui contiendra l'instance de la connexion
    protected $_connexion;

    // Propriétés permettant de personnaliser les requêtes
    public $table;
    public $id;
    public $title; 
    public $data;
    /**
     * Fonction d'initialisation de la base de données
     *
     * @return void
     */
    public function getConnection(){
        // On supprime la connexion précédente
        $this->_connexion = null;

        // On essaie de se connecter à la base
        try{
            $this->_connexion = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->_connexion->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Erreur de connexion : " . $exception->getMessage();
        }
    }

    /**
     * Méthode permettant de supprimer un enregistrement de la table choisie en fonction d'un id
     *
     * @return boolean
     */
     public function delete(){
        $sql = "DELETE FROM ".$this->table." WHERE id=".$this->id;
        $query = $this->_connexion->prepare($sql);
        if ($query->execute()) {
           return true;
        }
        return false;    
     }

    /**
     * Méthode permettant de modifier un enregistrement de la table choisie en fonction d'un id
     *
     * @return void
     */
     public function update(){
        $sql = $this->updateSQL() ;
        //echo "&emsp;" . $sql;
        //$query = $this->_connexion->prepare($sql);
        if ( $this->_connexion->query($sql) ) {
           return $this->id;
        }
        return false;    
     }

     public function updateSQL() {
        $sql = "";
        $fields = $this->getColumnsNames();
        $values = $this->data;
        if (count($fields) == count($values) ) { 
           $nb_elements = count ($this->data);
           for ($i=1; $i<$nb_elements; $i++) {
	              // on accede aux éléments du tableau $tableau1
               $type = $this->fieldType($i);
               if ( $this->isNumerics($type) ) {
	                 $sql .= "," . $fields[$i] . "=" .  $values[$fields[$i]] ;
               }else {
	                 $sql .= "," . $fields[$i] . "='" .  $values[$fields[$i]] . "'";
               }
           }
           $sql = "UPDATE " . $this->table . " SET " . substr($sql,1) . " WHERE " . $fields[0] . "=" .  $values[$fields[0]] . "";
           $this->id = $values[$fields[0]];
           return $sql;
        }      
        return false;
     }

     /**
      * 
      * 
      **/
     public function execSQL($sql){
        $sqlType = 'SELECT';
        //$query = $this->_connexion->query($sql);
        if ( $this->_connexion->query($sql) ) {
           return $this->_connexion->lastInsertId();
        }
        return false;
     }

     /**
      * 
      * 
      **/
     public function insert(){
        $sql = $this->insertSQL() ;
        //echo $this->insertValues();
        //var_dump($this->data);
        //echo $sql;

        
        if ( $this->_connexion->query($sql) ) {
           return $this->_connexion->lastInsertId();
        }
        return false;

     }

     public function getColumnsNames(){
        try {
           // get column names
           $query = $this->_connexion->prepare("DESCRIBE " . $this->table );
           $query->execute();
           $fields = $query->fetchAll(PDO::FETCH_COLUMN);
           return $fields;
        } catch(PDOExcepetion $e) {
           echo $e->getMessage();
        }
     }

     public function insertFields() {
        $sql = '';
        $fields = $this->getColumnsNames();
        for ( $i = 1; $i < count($fields); $i++ ) {
            $sql .= ',`' . $fields[$i]. '`';
        }
        return substr($sql,1);
     }

     public function insertValues() {
        $sql = '';
        $fields = $this->getColumnsNames();
        //var_dump($fields);
        $values = $this->data;
        if ( count($fields) == count($values)) { 
           $nb_elements = count ($this->data);
           for ($i=1; $i<$nb_elements; $i++) {
	              // on accede aux éléments du tableau $tableau1
               $type = $this->fieldType($i);
               //echo $type . " is ". $this->isNumerics($type) ;
               if ( $this->isNumerics($type) ) {
                  $sql .= "," .  $values[$fields[$i]] ;
               }else {
                  $sql .= ",'" .  $values[$fields[$i]] . "'";
               }
               /*
               if ( $this->isNumerics($type) ) {
	                 $sql .= "," .  $values[$i];
               }else {
	                 $sql .= ",'" .  $values[$i] . "'";
               }
               */
           }
           //echo $sql;
           return substr($sql,1);
       }
       return false;
    }

    public function insertSQL() {
       $sql = '';
       $wrapperFields = $this->insertFields();
       $wrapperValues = $this->insertValues();
       $sql =  "INSERT INTO `" . $this->table . "`(" . $wrapperFields . ") VALUES (" . $wrapperValues . ");";
       
       return $sql;
    }
/*    
native_type has the following mapping for MySQL:
(tested in PHP 5.4, MySQL 5.7)

INT(11) (PKs) => LONG
TINYINT(4)    => TINY
DOUBLE        => DOUBLE
VARCHAR       => VAR_STRING
CHAR          => STRING
DATE          => DATE
Functions     => VAR_STRING
- DATEFORMAT()
- CONCAT()

$datatypes = array(
        MYSQLI_TYPE_TINY => "TINY",
        MYSQLI_TYPE_SHORT => "SHORT",
        MYSQLI_TYPE_LONG => "LONG",
        MYSQLI_TYPE_FLOAT => "FLOAT",
        MYSQLI_TYPE_DOUBLE => "DOUBLE",
        MYSQLI_TYPE_TIMESTAMP => "TIMESTAMP",
        MYSQLI_TYPE_LONGLONG => "LONGLONG",
        MYSQLI_TYPE_INT24 => "INT24",
        MYSQLI_TYPE_DATE => "DATE",
        MYSQLI_TYPE_TIME => "TIME",
        MYSQLI_TYPE_DATETIME => "DATETIME",
        MYSQLI_TYPE_YEAR => "YEAR",
        MYSQLI_TYPE_ENUM => "ENUM",
        MYSQLI_TYPE_SET    => "SET",
        MYSQLI_TYPE_TINY_BLOB => "TINYBLOB",
        MYSQLI_TYPE_MEDIUM_BLOB => "MEDIUMBLOB",
        MYSQLI_TYPE_LONG_BLOB => "LONGBLOB",
        MYSQLI_TYPE_BLOB => "BLOB",
        MYSQLI_TYPE_VAR_STRING => "VAR_STRING",
        MYSQLI_TYPE_STRING => "STRING",
        MYSQLI_TYPE_NULL => "NULL",
        MYSQLI_TYPE_NEWDATE => "NEWDATE",
        MYSQLI_TYPE_INTERVAL => "INTERVAL",
        MYSQLI_TYPE_GEOMETRY => "GEOMETRY",
    );
*/
    public function fieldType($colIndex){
       $select = $this->_connexion->query('SELECT * FROM '. $this->table . "");
       $meta = $select->getColumnMeta($colIndex);
       return $meta["native_type"];
    }

    public function isNumerics($type) {
       $datatype = array("LONG","TINY");
       if ( in_array($type, $datatype) ) {
          return true;
       }     
       return false;
    }
/*
    public function isNumerics($type) {
       $datatype = array(
          'BIT' => 16,
          'TINYINT' => 1,
          'BOOL' => 1,
          'SMALLINT' => 2,
          'MEDIUMINT' => 9,
          'INTEGER' => 3,
          'BIGINT' => 8,
          'SERIAL' => 8,
          'FLOAT' => 4,
          'DOUBLE' => 5,
          'DECIMAL' => 246,
          'NUMERIC' => 246,
          'FIXED' => 246
       );

       if ( in_array($type, $datatype) ) {
          return true;
       }     
       return false;
    }

    public function isDates($type) {
       $datatype = array(
          'DATE' => 10,
          'DATETIME' => 12,
          'TIMESTAMP' => 7,
          'TIME' => 11,
          'YEAR' => 13
       );
       if ( in_array($type, $datatype) ) {
          return true;
       }     
       return false;
    }

    public function isStringsOrBinary($type) {
       $datatype = array(
          'CHAR' => 254,
          'VARCHAR' => 253,
          'ENUM' => 254,
          'SET' => 254,
          'BINARY' => 254,
          'VARBINARY' => 253,
          'TINYBLOB' => 252,
          'BLOB' => 252,
          'MEDIUMBLOB' => 252,
          'TINYTEXT' => 252,
          'TEXT' => 252,
          'MEDIUMTEXT' =>252,
          'LONGTEXT' => 252
       );
       if ( in_array($type, $datatype) ) {
          return true;
       }     
       return false;
    }

    /**
     * Méthode permettant d'obtenir un enregistrement de la table choisie en fonction d'un id
     *
     * @return void
     */
     public function getFirst(){
        $sql = "SELECT * FROM ".$this->table." ORDER BY Id ASC LIMIT 1";
        $query = $this->_connexion->prepare($sql);
        $query->execute();
        return $query->fetch();    
     }

    /**
     * Méthode permettant d'obtenir un enregistrement de la table choisie en fonction d'un id
     *
     * @return void
     */
     public function getLast(){
        $sql = "SELECT * FROM ".$this->table." ORDER BY Id DESC LIMIT 1";
        $query = $this->_connexion->prepare($sql);
        $query->execute();
        return $query->fetch();    
     }

    /**
     * Méthode permettant d'obtenir un enregistrement de la table choisie en fonction d'un id
     *
     * @return void
     */
     public function findByTitle(){
        $sql = "SELECT * FROM ".$this->table." WHERE Title='".$this->title . "'";
        $query = $this->_connexion->prepare($sql);
        $query->execute();
        return $query->fetch();    
     }

    /**
     * Méthode permettant d'obtenir un enregistrement de la table choisie en fonction d'un id
     *
     * @return void
     */
     public function findById(){
        $sql = "SELECT * FROM ".$this->table." WHERE Id=".$this->id;
        $query = $this->_connexion->prepare($sql);
        $query->execute();
        return $query->fetch();    
     }

    /**
     * Méthode permettant d'obtenir tous les enregistrements de la table choisie
     *
     * @return void
     */
     public function getAll(){
        $sql = "SELECT * FROM ".$this->table;
        $query = $this->_connexion->prepare($sql);
        $query->execute();
        return $query->fetchAll();    
     }

}

?>