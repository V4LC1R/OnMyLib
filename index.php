<?php
define('PATH_PRIVATE',dirname(__FILE__) );

include PATH_PRIVATE ."/SQL/insert.php";
include PATH_PRIVATE ."/SQL/select.php";
include PATH_PRIVATE ."/SQL/update.php";
include PATH_PRIVATE ."/SQL/delect.php";

class Model {
    protected $settings;
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

    private function setClassName($name){
        $this->class_name = strtolower($name);
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

    private function onRequest(){
        //verifica os campos passados na req
        //$this->verifyColuns();

        //verifica os typos dos campos
        //$this->verifyTypes();

        //verifica se todos os campos obrigatórios foram pegos
        //$this->verifyAllowNull();

        //verifica se as chaves foram passadas
        //$this->verifyForings();

        //etapa anti SQL Injection
       
        return $this->data;
    }

    private function verifyColuns(){

    }

    private function verifyTypes(){

    }

    private function verifyAllowNull(){

    }

    private function verifyForings(){
        
    }

    private function antiInjection(){

    }

    public function create( array $values){
        //$this->data = $values;

        $this->onRequest();

        //$reValues = settingNULL($values)

        $insert = new Insert($this->class_name,$this->coluns_types);


        $insert->settingsAll($values);

        $insert->GoInsertPDO();

    }
    
}

?>