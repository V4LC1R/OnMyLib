<?php

class Res {
    public function byJSON($RESULT){
        $data= array();
        var_dump($RESULT);
        while($row = $RESULT->fetch(PDO::FETCH_ASSOC)){
            foreach($row as $key => $val){
                if (is_null($val)) $val = '';
                $d["$key"] = $val;
            }
            array_push($data, $d);
        }
        echo json_encode($data);
    }
    public function byNULL(){

    }
}

?>