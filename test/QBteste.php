<?php
    include '../SQL/QuerryBuilderV2.php';

class QuerryBuilderTeste extends DaviORM{
    public function SelectValidation(){
        //select * from Tata where id = :id255
      $test =  $this->setEntity("Tata")->Select()->Where(["id"=>1]);
      $test->ExecuteSqlToSelect();
      return ;
    }

    public function InsertValidation(){

    }
}

$r = new QuerryBuilderTeste();

$r->SelectValidation();

/*

if(!$this->clausures["aux"][strtoupper($clausure)])
            $this->clausures["aux"][strtoupper($clausure)] = $args[0];
        if(count($args)>1)
            $this->clausures["aux"] = $args;
 */
?>

