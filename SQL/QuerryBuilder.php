<?php
require_once PATH_PRIVATE.'/dataTypes.php';
require_once PATH_PRIVATE.'/connection.php';


//classe criada unicamente para 
class DataBase extends DataTypes{

//<----- Global atributes ------->
    private $coluns_toSQL=[];

    private $data;

    private $table;

    private $connection;

    private $types;

    private $WHEREs;

    private $ORDERby;

    private $GROUPby;

    private $binds=[];

//<----- Querry to Execute ------>
    private $sql;


//<----------- Construct base --------------->
    public function __construct(array $types){

    $this->setTypesValues($types);
   
  }
///<------------------ Global --------------->
  protected function setTypesValues(array $types=[]){
    return $this->types=$types;
  }
  
  protected function setTableName(string $table_name = null){
    return $this->table = $table_name;
  }

  private function getTableName(){
    return "`{$this->table}`";
  }

  protected function settingsColunsValues(array $values =[]){
      $this->coluns_toSQL=array_keys($values);
      $this->data=$values;
  }

  private function setBinds(){
   
   $pdo= $this->connection->prepare($this->sql);
    if(empty($this->binds))
      return $pdo;

    foreach ($this->binds as $key => $value) {
      $this->setValue($value[1]);
      $t = $value[0];
      if($value[1]==null)
        return $pdo->bindValue($key,null);
      $pdo->bindValue($key,$this->$t());
    }

    return $pdo;
  }

///<-------------- All settings for Insert ---------->

  private function setTableToInsert(){
    return $this->sql.= "INSERT INTO ".$this->getTableName()."";
  }

  private function setColunsToInsert(){      
        return $this->sql.= ' ('.implode(',',$this->coluns_toSQL).')';
  }

  private function setRefsInsert(){
    foreach ($this->data as $key => $value) {
      $t[]=":{$key}"; 
      $this->setValue($value);
      $typing = $this->types["{$key}"];
      $this->binds[":{$key}"]=[$typing,$value];
    }
    $this->sql.= " VALUES (".implode(',',$t).')';
    
  }

///<-------------- All seting to Select ------------------>


  private function setColunsToSelect(){
    if($this->data == [])
      return $this->sql .= "SELECT * ";

    for ($i=0; $i <count($this->data) ; $i++) { 
      $colun = $this->data[$i];
      $this->sql.= "`{$colun}`,";
    }
    return $this->sql= substr($this->sql,0,-1);
    
  }

  private function setTableToSelect(){
    return $this->sql.="FROM {$this->getTableName()}";
  }

  private function setWhereClosure(){
    if($this->WHEREs==[])
      return null;

    $t;
    foreach ($this->WHEREs as $key => $value) {
      $t[]= "`{$key}`=:{$key}";
      $typing = $this->types["$key"];
      $this->binds[":{$key}"]=[$typing,$value];
    }
    $this->sql.= "WHERE ".implode("AND",$t);
  }

  /*
  
  protected function setOrderByClosure(){}
  protected function setGrouByClosure(){}
  */
  
///<------------- action pure --------------->
  protected function Insert(){
    $conn = new Connect();
    $this->connection= $conn->getConnection($conn->byPDO());
    $this->setTableToInsert();
    $this->setColunsToInsert();
    $this->setRefsInsert();
    $this->setBinds()->execute();
    var_dump($this->sql);
  }
  
  protected function Select(){
    $conn = new Connect();
    $this->connection= $conn->getConnection($conn->byPDO());
    $this->setColunsToSelect();
    $this->setTableToSelect();
   // $this->setWhereClosure();
    //$this->setOrderBy();
    //this->setGrouBy();
  // $a= $this->connection->prepare($this->sql);
    //$a->execute();
   // $b->fetchAll();
    //var_dump($this->sql);
    $a=$this->setBinds();
    $a->execute();
    $data =array();
    while($row = $a->fetch(PDO::FETCH_ASSOC)){
      foreach($row as $key => $val){
          if (is_null($val)) $val = '';
          $d["$key"] = $val;
      }
          array_push($data, $d);
    }
    echo json_encode($data);
  }



  }


?>