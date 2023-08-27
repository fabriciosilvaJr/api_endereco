<?php 
	class Municipios {



        public function listar (){

       function gerarSQLConsultarListar($params){
          $parametros  = [];
          $sql = 'SELECT  CODIGO_MUNICIPIO, CODIGO_UF, NOME, STATUS FROM TB_MUNICIPIO WHERE 1 = 1 ';
          unset($params['path']);
          //print_r($params);

          if(!is_null($params['codigoMunicipio'])){
           $sql .= ' AND CODIGO_MUNICIPIO = :codigoMunicipio ';
          }

          if(!is_null($params['codigoUF'])){
           $sql .= ' AND CODIGO_UF = :codigoUF ';
          }

          if(!is_null($params['nome']))
          {
           $sql .= ' AND nome = :nome ';
          }
          if(!is_null($params['status']))
          {
           $sql .= "AND STATUS = :status ";

          }
          $sql .= " ORDER BY CODIGO_MUNICIPIO DESC ";
          return [$sql, $params];   
        }

         $db = new DB(); 

          
           parse_str($_SERVER['QUERY_STRING'], $parameters);

           $recursos = gerarSQLConsultarListar($parameters);
           $sql = $recursos[0]; //sql
           $parametros  = $recursos[1]; 

           //var_dump($recursos);
           $rs = $db ->connect()->prepare($sql);
           $rs->execute($parametros);

           $error= $rs->errorInfo();

              if($error[0] !="00000" ){
                  echo json_encode(array("mensagem" => "Não foi possível consultar UF no banco de dados.", "status" => 404));
                  http_response_code(404); exit;

             }
	        //var_dump($error);
	        // var_dump($rs->debugDumpParams());
           $obj =  $rs-> fetchAll(PDO::FETCH_ASSOC);

            $result = array_map(function ($data) {
                  return [     
                    'codigoMunicipio'       => $data['codigo_municipio'],
                    'codigoUF'     => $data['codigo_uf'],
                    'nome' => $data['nome'],
                    'status'    => $data['status'],
                  ];
                }, $obj);

              if((count($obj) == 1 && !is_null($parametros['codigoMunicipio']))) {
                  if(count($obj) > 0){

                    $umRegistro = [
                      'codigoMunicipio'   => $obj[0]['codigo_municipio'],
                      'codigoUF'     => $obj[0]['codigo_uf'],
                      'nome' => $obj[0]['nome'],
                      'status'    => $obj[0]['status'],
                    ];

                    echo json_encode($umRegistro);
                    exit;
                    


                  } 
                  
                }else {
                  echo json_encode($result);
                  exit;


                }

                  if(count($parametros) == 0){
                  echo json_encode($result);
                  

                } 

                else if(count($result) == 0 || count($parametros) == 0){
                    echo json_encode([  ]);


                } 




        }

	}


 ?>