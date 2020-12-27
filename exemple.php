<?php
    $r =  " SELECT * FROM table WHERE colunaA >= x AND ( colunaB >= ZZ or colunaC <= YY) and colunaD like '%XXX%'";
    $t = ["distinc"=>false,
        "where"=>[
            "colunaA"=>[
                "MaI"=>"x"
            ],
            "Or"=>[
                "colunaB"=>[
                    "MaI"=>"ZZ",
                ],
                "ColunaC"=>[
                    "MeI"=>"YY"
                ]
            ],
            "colunaD"=>[
                "pLikeq"=>"%XX%"
            ]
            
        ]
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
                    "outra"=>["Diff"=> WWW]
                ],
                [
                    "state"=>"DANG",
                    "outra"=>["Diff"=> YYY]
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