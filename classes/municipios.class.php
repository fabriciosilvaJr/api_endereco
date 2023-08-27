<?php 
	class Municipios {

     function teste(){
       echo "teste";
     }



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
                  echo json_encode(array("mensagem" => "Não foi possível consultar MUnicipio no banco de dados.", "status" => 404));
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

        public function adicionar(){
        	$req = json_decode(file_get_contents('php://input'), true);
        	$db = new DB();
        	$conexao = $db ->connect();

          $rsUF =  $conexao->prepare("select * from tb_uf WHERE codigo_uf = :codigoUF");
          $rsUF->execute(array(
            'codigoUF' => $req['codigoUF']
          ));
          $resultUF =  $rsUF -> fetchAll();

          if (count($resultUF)== 0) {
            echo json_encode(array("mensagem" => "Não foi possível incluir Municipio no banco, pois ainda não existe uma uf cadastrada para esse código", "status" => 404));
            http_response_code(404); exit;

          }
          $rsNome =  $conexao->prepare("select * from tb_municipio WHERE NOME= :nome");
          $rsNome->execute(array(
            'nome' => $req['nome']
          ));
          $resultNome =  $rsNome-> fetchAll();
           //var_dump($resultSigla);

          if (count($resultNome)> 0) {
            echo json_encode(array("mensagem" => "Não foi possível incluir muicipio no banco, pois já existe um municipio com esse mesmo nome", "status" => 404));
            http_response_code(404); exit;

          }


          if (is_null($req['codigoUF'])) {
           echo json_encode(array("mensagem" => "Não foi possível incluir municipio no banco, pois o campo codigoUF é obrigatório", "status" => 404,   "nomeDoCampo" =>"codigoUF"));
           http_response_code(404); exit;
           
         }
         if (is_null($req['nome'])) {
           echo json_encode(array("mensagem" => "Não foi possível incluir municipio no banco, pois o campo nome é obrigatório", "status" => 404,   "nomeDoCampo" =>"nome"));
           http_response_code(404); exit;
           
         }
         if (is_null($req['status'])) {
           echo json_encode(array("mensagem" => "Não foi possível incluir municipio no banco, pois o campo status é obrigatório", "status" => 404,   "nomeDoCampo" =>"status"));
           http_response_code(404); exit;
           
         }

         $rs =  $conexao->prepare("INSERT INTO tb_municipio (codigo_municipio, codigo_uf,  nome, status) Values (nextval('sequence_municipio'), :codigoUF, :nome, :status)");

         $exec =  $rs->execute($req);
         $error= $rs->errorInfo();
        	  //var_dump($error);

         if($exec){
           $municipios = new Municipios();
           $municipios ->listar();

        } else{
          echo json_encode(array("mensagem" => "Não foi possível incluir MUnicipio no banco de dados.", "status" => 404));
          http_response_code(404); exit;


        }

      }

      public function alterar(){
        $req = json_decode(file_get_contents('php://input'), true);
                 //var_dump(array_values($req));
                // var_dump($data['sigla']);
        $db = new DB();

        $conexao = $db ->connect();

        $rs =  $conexao->prepare("UPDATE tb_municipio set codigo_uf = :codigoUF, nome= :nome, status= :status WHERE codigo_municipio = :codigoMunicipio ");
        $exec =  $rs->execute($req);

        $error= $rs->errorInfo();
          //var_dump($error);
          //var_dump($rs->debugDumpParams());
        if($exec){
         $municipios = new Municipios();
         $municipios ->listar();
         

       } else{
        echo json_encode(array("mensagem" => "Não foi possível alterar municipio no banco de dados.", "status" => 404));
        http_response_code(404); exit;


      }

    }

    public function deletar($param){
      $db = new DB();
      $conexao = $db ->connect();
      $rs =  $conexao->prepare("select * from tb_municipio where codigo_municipio = :codigoMunicipio");
      $rs->execute(array(
        'codigoMunicipio' => $param
      ));
      $verificaMP =  $rs-> fetchAll(PDO::FETCH_ASSOC);
      //var_dump($verificaMP);
       //var_dump($verificaUF);

      if (count($verificaMP) > 0) {
        $rs =  $conexao->prepare("UPDATE tb_municipio set status = 2 WHERE codigo_municipio = :codigoMunicipio");
        $exec =  $rs->execute(array(
          'codigoMunicipio' => $param
        ));
        if($exec){
          $municipios = new Municipios();
          $municipios ->listar();


        } else{
          echo json_encode(array("mensagem" => "Não foi possível desativar Municipio no banco de dados.", "status" => 404));
          http_response_code(404); exit;


        }


      } else{
        echo json_encode(array("mensagem" => "Não foi possível encontrar um municipio com o código informado.", "status" => 404));
        http_response_code(404); exit;
      }

    }


	}


 ?>