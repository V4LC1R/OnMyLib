<?php
    $r =  " SELECT * FROM table WHERE colunaA >= x AND ( colunaB >= ZZ or colunaC <= YY) and colunaD like '%XXX%'";
    $t = ["distinc"=>false,
        
            // "id"=>4,
            //"idd"=>5,
            /*"Or"=>
            [
                "id"=>2,//lv1
                "idd"=>7
            ],*/
            //campo1
            "Or"=>
            [//valor1
                //campo2
                [//lv2-valor2
                    //campo3
                    "id"=>2//valor3
                    ,"nn"=>4
                ],
                ["hp"=>7,"rr"=>9]
            ],
           /* "or"=>
            [
                "id"=>["MaI"=>4],//lv1 +
                "id"=>["MeI"=>9]
            ],
            "Or"=>[
                "id"=>[1,2],
                "id"=>["MeI"=>1,"pLIKE"=>2]
            ],*///..wher ((id >= 4 or id <= 9 or id =9) or (id>=4 or ik <= 9))
            "or"=>
            [
                [
                    "id"=>["MaI"=>4],//lv2 +
                    "id"=>["MeI"=>9],
                    "id"=>9
                ],
                [
                    "id"=>["MaI"=>4],
                    "ik"=>["MeI"=>9]
                ]
            ],           
           /* "id"=>[
                "Or"=>[2,3]//basico
            ],
            "id"=>[
                "Or"=>[
                    "MeI"=>2,
                    "pLike"=>3
                ]
            ]*/
            /*"uid"=>[
                "MeI"=>2
            ]*/
        
    ];

    $e = "SELECT * FROM table WHERE ( colunaA = 'dd' or colunaA = 'CC')";
    $d=[
        "where"=>[
            "colunaA"=>[
                //or lv 3
                "Or"=>[ " dd ", " CC "]
            ]
        ]
    ];


    $f = "SELECT FROM WHERE ( (state ='safe' and outra <> 'WWW') OR (state ='DANG' and outra <> 'YYY') )";
    $o=[
        "where"=>[
            "Or"=>[
                [
                    "state"=>"SAFE",
                    "outra"=>["Diff"=> 'WWW']
                ],
                [
                    "state"=>"DANG",
                    "outra"=>["Diff"=> 'YYY']
                ]
            ]
        ]
    ];
    

    $h="SELECT * FROM table WHERE (colunaA = 'XXX' or ( colunab = 'cgd' and colunaf='44a')) ";

    $g=[
        "where"=>[
           "Or"=>[
               "colunaA"=>'xxx',
               [
                   "colunaB"=>'cgd',
                   "colunaF"=>'44a'
               ]
           ]
        ]
    ];

    $r="SELECT * FROM table WHERE ( ( colunaA>= 1 and colunaA <= 4) or (colunaA = 'dd' or colunaA = 'CC') )";
    $v=[
        "where"=>[
            "Or"=>[
                "colunaA"=>[
                    "MaI"=>1,
                    "MeI"=>2
                ],
                "colunaA"=>[
                    'dd',
                    'CC'
                ]
            ]
        ]
    ];
    
    
?>