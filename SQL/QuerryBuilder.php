<?php
require_once PATH_PRIVATE.'/dataTypes.php';
require_once PATH_PRIVATE.'/connection.php';


//classe criada unicamente para 
class DataBase extends DataTypes{

//<----- Global atributes ------->
    private $coluns_toSQL=[];

    private $data;

    private $table;

    private $connection ;

    private $types;

    private $useClosure = [];

    private $whereOperations = [];

    private $whereQuerry ;

    private $orderQuerry;

    private $groupQuerry;

    private $likeQuerry;

    private $havingQuerry;

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

  protected function setOnlyColuns( array $coluns){
   return $this->coluns_toSQL = $coluns;
  }

  protected function settingsColunsValues(array $values =[]){
      $this->coluns_toSQL=array_keys($values);
      $this->data=$values;
  }

  protected function getSql(){
    return $this->sql;
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

  private function buildRefsInsert(){
    foreach ($this->data as $key => $value) {
      $t[]=":{$key}"; 
      $this->setValue($value);
      $typing = $this->types["{$key}"];
      $this->binds[":{$key}"]=[$typing,$value];
    }
    $this->sql.= " VALUES (".implode(',',$t).')';
    
  }

///<-------------- All seting to Select ------------------>

//SELECT <DISTINCT> FROM {TABLE} <WHERE> <LIKE> <%%> 
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

  

  private function WhereClosure(){
   $t = [];
    foreach ($this->whereQuerry as $key => $value){
      if(is_array($value))
       // $t = $this->buildOperation($value);
      $t[]= "`{$key}`=:{$key}";
      $typing = $this->types["$key"];
      $this->binds[":{$key}"]=[$typing,$value];
    }
   return $this->sql.= " WHERE ".implode("AND",$t);
  }
  
//<<<---- operataror ---->>>
/*
  aqui contruiremos nossos contrutores de consuta como
  comparadores, clausuras "OR" "groupby" "OrderBy" "Having" "Like" e "%XXXX%"
*/
  private function buildClosures(array $data){
    //join method
    //$this->Join($data)
    $this->opWhere($data);
    $this->opGroupBy(/*$data*/);
    $this->opHaving(/*$data*/);
    $this->opOrderBy(/*$data*/);
  }

  private function opWhere($data){
    //verifica se existe o where dentro do "data"
    if(!in_array("where",$data))
      return 
    
    //seta q a clausura vai ser utilizada
    $this->useClosure[]="where";

    //pegas os itens para a clausulas
    $where = $data["where"];

    
    //invoca os metodos de comparação "or"
    $this->compareOr($where);

    $t = [];
    $opSignal = "=";

    foreach ($where as $campo => $value) {

      $absoluteValue = $value;
      //evita um "Or" lv 1
      if($campo == "Or")
        return;

      //evita um "Or" lv2
      if(is_array($value) and array_key_first($value) == "Or")
        return;

      //verifica se o campo passado existe na relação
      if(!in_array($campo,$this->coluns_toSQL))
        return;

      //se for array, vai pegar o tipo de operação
      if(is_array($value)){
        //pegar a key, que provavelmente é um nome de operador
        $op =array_key_first($value);
      
        //vai setar o nome da função de operação
        $compare = "compare{$op}";

        //vai setar o opSignal com o sinal da função de operação
        $opSignal = $this->$compare();

        //vai subtituir o absoluteValue, buscando dentro do array
        $absoluteValue = $value[$op];
      }

      // aqui ele constroi a string para o build, sentando o bind
      $t[]= "`{$campo}`{$opSignal}:{$campo}";

      //aqui ele passa a função de tipagem
      $typing = $this->types["$campo"];

      //aqui ele armazena os campos que foram bindados,junto com as suas funções de tipagem
      return $this->binds[":{$campo}"]=[$typing,$absoluteValue];
    }

    // por ultimo constroi a querry de where sem o operador "or"
    $this->whereQuerry .= implode("AND",$t);
    
  }

  private function buildWhereClosure(){
    //<
  }

  

  private function opOrderBy(){}

  private function opGroupBy(){}

  private function opHaving(){}

  
  //private function opCount(){}

  // vai percorrer o array inteiro em todas as dimensões, para procurar o seu operador
  private function compareOr(array $data){
    $baseOperators = ["Maior","Menor","MaI","MeI","Diff","pLike","pLikeq","Likeq"];

    

    $lv1 =[];
    $lv2 =[];
    $lv3 =[];
      foreach ($data as $campo => $compare) {
        //não é array? sim== vaza não == continua
        if(!is_array($compare))
          return;

        //se o $campo e key_f for diff de "Or" == vaza
        if($campo !== "Or" and array_key_first($compare)!== "Or")
          return;

        //or de lv1
        if($campo == "Or"){

          /*vou pegar as definições de consulta "Or"
            verficar no looping se é um array, para aplicar um operador logico
          */
          foreach ($compare as $key => $value) {

           //verifica se é um array
            if(is_array($value)){
              //percorro
              foreach ($value as $chave => $valor) {
                //pegar as definições do Or daquele campo
                if(in_array($chave,$this->coluns_toSQL)){
                  //quando tem um operador (>,>=,<=,<>) ou mais definições
                  if(is_array($valor)){
                    //quando for exc um Or entre valores de um mesmo campo
                    foreach($valor as $defines=> $last){
                      //verifica se está sendo inferido um operador logico
                      if(in_array($defines,$baseOperators)){

                      }else{
                        $lv1[] = "`{$key}=:{$key}B`";
                        $typing = $this->types["$key"];
                        return $this->binds[":{$key}B"]=[$typing,$value];
                      }
                    }
                  }

                }
                  
                
              }


            }

            /*
              Se não for, vai verificar se o campo passado existe na definição de campos
            */
            if(!in_array($key,$this->coluns_toSQL))
              return;
              
            //building a querry or
            $lv1[] = "`{$key}=:{$key}A`";
            $typing = $this->types["$key"];
            return $this->binds[":{$key}A"]=[$typing,$value];
          }
        }

          
      }

    //  if($lv1)

  }


  private function callOperator($data){

    
  }
 
  private function compareMaior($campos,$closure = "where"){
    // setar o campo para n repetir

    // retornar 
  }

  private function compareMenor($campos,$closure = "where"){
    
  }

  private function compareMaI($campos,$closure = "where"){
    
  }

  private function compareMeI($campos,$closure = "where"){

  }

  private function compareDiff($campos,$closure = "where"){

  }

  private function compareLike($campos,$closure = "where"){

  }

  private function callClosures(){

  }

  
  
///<------------- action pure --------------->
  protected function Insert(){
    $conn = new Connect();
    $this->connection= $conn->getConnection($conn->byPDO());
    $this->setTableToInsert();
    $this->setColunsToInsert();
    $this->buildRefsInsert();
    $this->setBinds()->execute();
    var_dump($this->sql);
  }
  /*
      Contruir uma forma de montar a SQL, S. F. J. INJ W OR 
  */

  // distinct
  protected function Select(array $closures){
    

  }

  protected function SelectDistinct(array $closures){
    
  }

  protected function raw(string $querry){
    $this->sql = $querry;

    $conn =new Connect();


  }

  
}

?>