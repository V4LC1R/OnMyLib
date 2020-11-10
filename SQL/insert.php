<?php

include PATH_PRIVATE .'/connection.php';
include PATH_PRIVATE ."/dataTypes.php";

class Insert extends Connect{

    private $coluns_toInsert=[];

    private $sql_toInsert;

    private $types_toInsert;

    private $data;

    private $connection;
  //basicamente apenas insere

  public function __construct(string $table,array $types){

    parent::__construct([
      "type"=>'mysql',
      "host"=>'localhost',
      "port"=>'3306',
      "name"=>'catalogo',
      "user"=>'root',
      "password"=>''
    ]);

    $this->setTableName($table);
    $this->setTypesValues($types);
   
  }

  private function setTableName(string $table_name){
    return $this->sql_toInsert = "INSERT INTO `{$table_name}`";
  }

  private function setTypesValues(array $types){
    return $this->types_toInsert=$types;
  }

  public function settingsAll(array $values){
      $this->coluns_toInsert=array_keys($values);
      $this->data=$values;
  }

  private function setColunsInSql(){      
        return $this->sql_toInsert .= ' ('.implode(',',$this->coluns_toInsert).')';
  }
  
  private function setValuesInSql(){
    $this->sql_toInsert .= ' VALUES (';
    foreach ($this->data as $key => $value) {
      $this->sql_toInsert.=":{$key},"; 
    }
    $this->sql_toInsert= substr($this->sql_toInsert,0,-1);
    $this->sql_toInsert.= ')'; 
  }

  private function setBindValue(){
    
    $this->setColunsInSql();
    $this->setValuesInSql();

    $pdo = $this->connection->prepare($this->sql_toInsert);

    foreach ($this->data as $key => $value) {
      $typing = new DataTypes($value);
      $type = $this->types_toInsert["{$key}"];
      if($value == NULL)
        $pdo->bindValue(":{$key}",NULL);
      else $pdo->bindValue(":{$key}",$typing->$type());
    }
    return $pdo;
  }

  public function GoInsertPDO(){
      $this->connection= $this->getConnection($this->byPDO());
      $this->setBindValue()->execute();

      var_dump($this->sql_toInsert);
  }

}
 

?>