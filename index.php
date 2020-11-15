<?php
define('PATH_PRIVATE',dirname(__FILE__) );

include_once PATH_PRIVATE ."/SQL/QuerryBuilder.php";

class Model extends DataBase{
    private $settings;
    private $coluns_model;
    private $coluns_types;
    private $foringKey;
    private $foringKey_types;
    private $allowNull;
    private $class_name;

    private $data;

    protected function __construct(array $coluns,$name){
        $this->init($coluns);
        $this->setClassName($name);
    }

    //guarda as definições vindas
    private function init( array $settings){

            // verifico a existencia destes campos dentro do array
            if(!array_key_exists("coluns",$settings) && !array_key_exists("associate",$settings))
                echo "\n Colum/associate not define";
    
            if(count($settings) >2)
                echo "Err, don´t use more than two positions in array";
         
            //chamo metodo seting das colunas de preecnhimento
            $this->setColuns($settings["coluns"]);
    
            //chamo metodo para pegar as colunas foring
            $this->setForings($settings["coluns"]);
    
            //pegos os campos obrigatórios
            $this->setAllowNull($settings["coluns"]);
    }

    private function setClassName($name){
        $this->class_name = strtolower($name);
    }

    private function setColuns(array $data){
        $this->coluns_model= array_keys($data);   
        $this->setColunsType($data);
    }

    private function setColunsType(array $data){
        foreach ($data as $key => $value) {
            $this->coluns_types[$key] =$value["type"];
        }
    }

    private function setForings(array $data){
        foreach ($data as $key => $value) {
            if(array_key_exists('foringKey',$data)){
                $this->foringKey[$key];
                $this->setForeigKeysTypes($key,$value["foringKey"]);
            }                          
        }
    }

    private function setForeigKeysTypes($key,array $ref){
        $this->foringKey_types[$key]=$ref;
    }
    
    private function setAllowNull(array $data){
        foreach ($data as $key => $value) {
            if(array_key_exists("allowNull",$value))
                $this->allowNull = $key;
            else return;
        }
    }

    private function setGlobalDate(array $data){
        if(!$this->verifyColuns($data))
            echo "Incongruencia";
       // if(!$this->verifyType($data))
         //   echo "Incongruencia";

        return $this->data=$data;
    }

    private function getGlobalDate(){
        return $this->data;
    }

    private function getClassName(){
        return ''.$this->class_name.'';
    }


    private function verifyColuns(array $data){
        $requestKeys = array_keys($data);
        $b =0;
        $c =true;
            while($b<count($requestKeys)){
                $compare = $requestKeys[$b];
                if(!in_array($compare,$this->coluns_model))
                    $c = false; break ;
                $b++;
            }
        return $c;
    }

    private function verifyType(array $data){

    }

    public function create( array $values){
        //invoco  A classe de inserção 
        $this->setTypesValues($this->coluns_types);

        //set os valores da classe, com as devidas verificações
        $this->setGlobalDate($values);

        //inserio o valor e coluna
        $this->settingsColunsValues($this->getGlobalDate());

        //iniro  a tabela
        $this->setTableName($this->getClassName());

        //chamo o metodo final
        $this->Insert();

    }

    public function findAll(){
        $this->setTableName($this->getClassName());
        $this->Select();
    }
    
}

?>