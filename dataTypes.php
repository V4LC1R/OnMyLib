<?php

class DataTypes {

    private $value;

    public function __construct($value){

        $this->setValue($value);
    }

    private function setValue($value){
        return $this->value = $value;
    }

    public function STRING(){
        return ''.$this->value.'';
    }

    public function NUMBER(){
        if(is_int($this->value))
            return $this->value;
        else echo 'erro porra';
    }

    public function DECIMAL(number $c=10,number $v=2 ){
        
    }

    public function TEXT(){

    }

}

?>