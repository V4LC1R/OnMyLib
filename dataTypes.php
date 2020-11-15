<?php

class DataTypes{

    private $value;
  
  
  
    protected function setValue($value){

        
        return $this->value = $value;
    }
  
    protected function STRING(){
        
        if(!is_string($this->value))
            return var_dump("Err:: This not a STRING");
        return  ''.$this->value.'';
    }
  
    protected function NUMBER(){
       
        if(!is_int($this->value))
            return var_dump("ERR::This not a Number");
        return $this->value;
    }
  
    protected function DECIMAL( ){
       
        if(!is_float($this->value))
            return var_dump("ERR::This not a DECIMAL");
        return $this->value;
    }
  
    protected function TEXT(){
  
    }
  
  }
  

?>