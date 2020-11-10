<?php

class Connect {
      
    private  $dbType;
    private  $dbHost;
    private  $dbPort;
    private  $dbName;
    private  $dbUser;
    private  $dbPassord;

    private $dbh;

    protected function __construct(array $Setting){
        $this->setType($Setting["type"]);
        $this->setHost($Setting["host"]);
        $this->setPort($Setting["port"]);
        $this->setName($Setting["name"]);
        $this->setUser($Setting["user"]);
        $this->setPassword($Setting["password"]);
    }
    //<----- settings ----->
    protected function setType(string $set){
        return $this->dbType = $set;
    }
    protected function setHost(string $set){
        return $this->dbHost=$set;
    }
    protected function setPort(string $set){
        return $this->dbPort=$set;
    }
    protected function setName(string $set){
        return $this->dbName=$set;
    }
    protected function setUser(string $set){
        return $this->dbUser=$set;
    }
    protected function setPassword(string $set){
        return $this->dbPassord=$set;
    }

    //<---- getters --->
    
    private function getType(){
        return $this->dbType;
    }
    private function getHost(){
        return $this->dbHost;
    }
    private function getPort(){
        return $this->dbPort;
    }
    private function getName(){
        return $this->dbName;
    }
    private function getUser(){
        return $this->dbUser;
    }
    private function getPassword(){
        return $this->dbPassord;
    }
    //
    protected function byPDO(){
       return new PDO(''.$this->getType().':host='.$this->getHost().';port='.$this->getPort().';dbname='.$this->getName().';',''.$this->getUser().'',''.$this->getPassword().'', array(PDO:: ATTR_PERSISTENT =>false));
    }

    protected function getConnection($connection){
        return  $this->dbh =$connection;
    }

    protected function byMYSQLI(){
        
    }

}

?>