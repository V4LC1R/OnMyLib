<?php
    include '../SQL/QuerryBuilder.php';

class QBteste extends QuerryBuilder{
    
    public function tInsert(){


        var_dump($this->getSql());
    }

    public function tSelect(){
        var_dump($this->getSql());
    }
}

$a= new QBteste();

$a->tInsert();
?>