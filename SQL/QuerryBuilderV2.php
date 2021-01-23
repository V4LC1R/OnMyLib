<?php

require_once '../connection.php';
require_once '../dataTypes.php';
class DaviORM{
    private $connect;
    private $binds=[];
    private $clausures =[];
    private $validClausure =[];
    private $globalSql=[];
    
    public function __call( $clausure, $args){
        //verifica se um methodo pricipal foi iniciado
        if(empty($this->validClausure) )
            return throw new \Exception("Main(Select,Delete,Upadate,Insert) method not declare");
        //verifica se a clausura declara foi iniciada, ou pertence ao grupo do metodo principal
        if(!in_array($clausure,$this->validClausure) or $clausure !== "Table")
            return throw new \Exception("The method not pertence this group clousule");
        
        if(!$this->clausures["aux"][strtoupper($clausure)])
            $this->clausures["aux"][strtoupper($clausure)] = $args[0];
        if(count($args)>1)
            $this->clausures["aux"] = $args;
        
                        
        return $this;
    }

# ----- Global methods ------
    private function setBinds($sql){
        $pdo= $this->connect->prepare($sql);
         if(empty($this->binds))
           return $pdo;
         foreach ($this->binds as $key => $value) {
           $t = $value[0];
           $tip = new DataTypes();
           $tip->setValue($value[1]);
           if($value[1]==null)
             return $pdo->bindValue($key,null);
           $pdo->bindValue($key,$tip->$t());
         }
         return $pdo;
    }

    private function Bind(string $ref, array $defines){
        $typing = $this->types[$defines["coluna"]];
        $this->binds[":{$ref}"]=[$typing,$defines["value"]];
    }

    /**
     * 1)  Metodo para criação de strings, com campos setados para serem bindados, e com o seus operadores logicos declarados
     * @param String  $op pega o nome do operador
     * @param String $campo é o nome do campo de referencia para o bind
     * @return String vai retornar a string com o operador + valor já bindado
     */
    private function Op($op = "Equal", array $settings){

        if(is_int($op))
            $op = "Equal";
        //um arrays com as minhas definições de operadores
        //operadores logicos simples
        $logicSimple = [
            "Equal"=>"=",
            "Diff"=>"<>",
            "Maior"=>">",
            "MaI"=>">=",
            "Menor"=>"<",
            "MeI"=>"<=",
            "Like"=> "LIKE "
        ];

        // operadores que usam arrays 
        $logicArray =[
            "Btw"=>function($set){
                //verifica se é um array
                if(!is_array($set))
                    return throw new \Exception("For BETWEEN closure, need be a array");
                
                // verifca se a coluna foi especificada
                if(! $set["coluna"])
                    return throw new \Exception("For BETWEEN closure, need be in array `coluna`");
                
                //verifica se o valor 1 e 2 foram passados para range
                if(!$set["value"])
                    return throw new \Exception("For BETWEEN closure, need be in array `valor1` and `valor2`");
            
                //armazeno o bind
                $values1 = $set["coluna"].rand(50,100);
                $this->Bind(
                    $values1,
                    [
                        "coluna"=>$set["coluna"],
                        "value"=>$set["value"][0]
                    ]
                );
                $values2 =$set["coluna"].rand(1,45);
                $this->Bind($values2,
                    [
                        "coluna"=>$set["coluna"],
                        "value"=>$set["value"][1]
                    ]
                );
                
                //retorno a string já com os campos bindados
                return "(".$set["coluna"]." BETWEEN :".$values1." AND :".$values2.")";
            },
            "NTI"=>function($set){
                //verifica se é um array 
                if(!is_array($set))
                    return throw new \Exception("For NOT IN closure, need be a array");
                
                //verifica se a coluna e valores foram passados
                if(! $set["coluna"] and !$set["value"])
                    return throw new \Exception("For NOT IN closure, need be in array `coluna` and `In`");
                
                $ins = [];
                //rod um loop para pegar todos os vaslores do in
                for ($i=0; $i < count($set["value"]); $i++) { 
                    $in = $set["value"][$i];

                    //:campoF0
                    $str = $set["coluna"].rand(101,150);
                    $ins[] = ":{$str}";

                    $this->Bind($str,
                        [
                            "coluna"=>$set["coluna"],
                            "value"=>$in
                        ]
                    );
                }
                return $set["coluna"]. " NOT IN (".implode(",",$ins).")";
            },
            "pLIKE"=>function($set){
                //verifico se foi passado um array com as configs
                if(!is_array($set))
                    return throw new \Exception("For LIKE closure, need be a array");
                
                //valida os campos vindos no array
                if(!in_array("coluna",$set) or !in_array("value",$set))
                    return throw new \Exception("For LIKE closure, need be in array ");
                // construo a string de Bind
                $stringDiff = $set["coluna"].rand(151,199);

                //Função de guardar o bind
                $this->Bind(
                    $stringDiff,
                    [
                        "coluna"=>$set["coluna"],
                        "value"=>$set["value"]
                    ]
                );

                return $set["campo"]." LIKE '%:".$stringDiff."'";

            }/*"%:$campo{$dif}"*/,
            "LIKEq"=>function($set){

                //verifico a se foi passado um array
                if(!is_array($set))
                    return throw new \Exception("For LIKE closure, need be a array");
                
                if(!in_array("coluna",$set) or !in_array("value",$set))
                    return throw new \Exception("For LIKE closure, need be in array ");
                $stringDiff =  $set["coluna"].rand(200,235);
                $this->Bind(
                   $stringDiff,
                    [
                        "coluna"=>$set["coluna"],
                        "value"=>$set["value"]
                    ]
                );

                return $set["campo"]." LIKE '%:".$stringDiff."%'";
            }/*":$campo{$dif}%"*/,
            "pLIKEq"=>function($set){
                if(!is_array($set))
                    return throw new \Exception("For LIKE closure, need be a array");
                
                if(!in_array("coluna",$set) or !in_array("value",$set))
                    return throw new \Exception("For LIKE closure, need be in array ");
                $stringDiff = $set["coluna"].rand(236,280);
                $this->Bind(
                    $stringDiff,
                    [
                        "coluna"=>$set["coluna"],
                        "value"=>$set["value"]
                    ]
                );

                return $set["campo"]." LIKE ':".$stringDiff."%'";
            }/*"%:$campo{$dif}%"*/,
            "INN"=> function($set){

            },
            "IsN"=>function($set){

            } 
        ];

                
        if(!in_array($op,$logicSimple) or !in_array($op,$logicArray))
           return throw new \Exception("This Logic Operation wasn't exist, chose one");

        //verifica onde se encaixa a operação
        if(in_array($op,$logicArray))
            return $logicArray[$op][$settings];
        

        $stringDiff = $settings["coluna"].rand(300,400);
        $this->Bind($stringDiff,
            [
                "coluna"=>$settings["coluna"],
                "value"=>$settings["value"]
            ]
        );

        return $logicSimple[$op]." :".$stringDiff;
    }

    /**
     * @param String $entity tabela ou entedidade, que vai ser utilizada para a querry
     * @return Obeject retornará o obejeto para continuar as clausulas ou finalização
     */

    protected function setEntity(string $entity){
        $this->clausures["entity"] = $entity;
        return $this;
    }
    /**
     * @param Array $campos  poder especificar quais campos deseja que seja retornado, se desejar fazer count, é só concatenar
     * @param Bool $distinct true for distinc closure
     * @return Object vai retorar o proprio objeto, para poder setar as clausulas desejaveis
     * 
     */
# ------ Querrys settings
    protected function Select(Array $campos = ["*"], Bool $distinct=false ){
        if($this->clausures["main"])
            return throw new \Exception("Main querry has already been declared");
        //setando as clausuras que o select aceita para controle, na hora da declaração no call
        $this->validClausure= ["Where","Join","Count",];
        
        //defino a clausura principal
        $this->clausures["main"] = "Select"; 

        //instanceio uma armazenador de SQL local
        $localSql = "" ;
  
        //verifica se vai ter distinct
        if($distinct == true)
            $localSql = "SELECT DISTINCT";
        else $localSql = "SELECT";

        //construo a querry, que está sendo montada no inicio do instaceamento
        $localSql .= implode(',',$campos)." FROM ".$this->clausures["entity"];

        //coloco a querry em um array para ser buildada na hora da execução
        $this->globalSql[]=$localSql;

        return $this;
    }

# ----- execute and build querry
    /**
     * @return Array vai retornar os dados da busca
     */
    protected function ExecuteSqlToSelect(){
    //SELECT * FROM <TABLE> <JOINs> <WHERE Or LogicOperators> <ORDER BY> <HAVING LogicOperators Count, Sum> <GROUP BY>
        $finalSql=[];
        //evita construir uma querry sem uma tabela definida
        if(!in_array("entity",$this->clausures))
            throw new \Exception("Entity not define, plase use setEntity function");
        $aux = in_array("aux",$this->clausures)? $this->clausures["aux"] : '';

        //constroi a o join dentro da querry
        if(in_array("JOIN",$aux)){
            $joins ='';
            foreach ($aux["JOIN"] as $join => $settings) {
                // verifico se colocaram mais de 2 referncias por junção
                if(count($settings)>2){
                    throw new \Exception("Err when definig the join closure, more than 2 references");
                    break;
                }
                // concateno o join
                $joins.= " $join ".$settings["coluna"];

                //
                $key = array_keys($settings);

                $joins.=" ON ".$key[0].".".$settings[$key[0]]."=".$key[1].".".$settings[$key[1]];
            }

            $finalSql[]=$joins;
        }

        //<JOIN> --> WHERE 
        if(in_array("WHERE",$aux)){
            $finalSql[]="WHERE ";
            $wheres =[];
            foreach ($aux["WHERE"] as $campo1 => $valor1) {
                // pegar campos com operadores logicos diff de "=" e Clausulas "Or"
                if(is_array($valor1)){

                    //bloco de execução de "Or"s ( lv1 , lv2, lv1 + , lv2 +)
                    if($campo1=="Or"){
                        foreach ($valor1 as $campo2 => $valor2) {
                            //caso tenha um operador 
                            if(!is_array($valor2)){

                            }
                        }
                    }

                    // Bloco de execução de "Or"s basicos
                    if(in_Array("Or",$valor1)){
                        $or1 = $valor1["Or"];
                        
                        if(count($or1)>2){
                            //para não ter mais de 2 valores passados
                            throw new \Exception("Err when definig the OR closure, more than two value per field");
                            break;
                        }
                        $basic3 = [];
                        foreach ($or1 as $opr => $val) {
                            
                            //verifico para ver se tem um operador nomeado
                            if(in_array($opr,["Diff","Maior","MaI","Menor","MeI","LIKE","Btw","NTI","pLIKE","LIKEq","pLIKEq"])){

                               //armazeno temporáriamente as strings geradas, para fazer um implode
                               return $basic3[] = $this->Op($opr,["coluna"=>$campo1,"value"=>$val]);  
                            }

                            //armazeno temporáriamente as strings geradas, para fazer um implode
                            return $basic3[] = $this->Op(settings:["coluna"=>$campo1,"value"=>$val]);
                        }
                        // construo um string  e armazeno nos wheres
                        return $wheres[] ="(".implode(" OR ",$basic3).")";
                    }

                    // para o psicopata não colocar 2 operadores
                    if(count($valor1)>1){
                        throw new \Exception("Err when definig the Where closure, more than one logical operator per field");
                        break;
                    }

                    //pego a string gerada pelo o operador
                    $basic2 = $this->Op(array_key_first($valor1),["coluna"=>$campo1,"value"=>$valor1]);
                    return  $wheres[]=$basic2;

                }

                $simple = $this->Op(settings:["coluna"=>$campo1,"value"=>$valor1]);

                return $wheres[]=$simple;

            }

        }

        if(in_array("ORDERBY",$aux)){

        }
        
        
    }
    /*
    [ ] --> refazer o JOIN
    [x] --> construir retorno dinamico de operadores
    [x] --> Bind dinamico
    [ ] --> construir logica de Or lv1


        ob->where([campo=>btw[1,2],Op=>[btw=>[],btw=>[]]])
    
    */

}
?>