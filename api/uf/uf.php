    <?php 


    if($api == 'uf'){
      if (Pessoas::verificar()) {
    	if($method == "GET"){

          $db = new DB(); 
          parse_str($_SERVER['QUERY_STRING'], $parameters);

          $recursos = gerarSQLConsultarListar($parameters);
              $sql = $recursos[0]; //sql
              $parametros  = $recursos[1]; 

              //var_dump($recursos);
              $rs = $db ->connect()->prepare($sql);
              $rs->execute($parametros);

              $error= $rs->errorInfo();
              //var_dump($error);
             // var_dump($rs->debugDumpParams());
              

              $obj =  $rs-> fetchAll(PDO::FETCH_ASSOC);

              //var_dump($obj[0]['codigo_uf']);

              $result = array_map(function ($data) {
                return [     
                  'codigoUF'       => $data['codigo_uf'],
                  'sigla'     => $data['sigla'],
                  'nome' => $data['nome'],
                  'status'    => $data['status'],
                ];
              }, $obj);


              if((count($obj) == 1 && is_null($parametros['status'])) ||
                !is_null($parametros['codigoUF'])||
                !is_null($parametros['sigla']) ||
                !is_null($parametros['nome'])){
                if(count($obj) > 0){

                  $umRegistro = [
                    'codigoUF'       => $obj[0]['codigo_uf'],
                    'sigla'     => $obj[0]['sigla'],
                    'nome' => $obj[0]['nome'],
                    'status'    => $obj[0]['status'],
                  ];

                  echo json_encode($umRegistro);


                } 
                
              }else if(!is_null($parametros['status'])){
                echo json_encode($result);

              }


              if($error[0] !="00000"){
                echo json_encode(array("mensagem" => "Não foi possível consultar UF no banco de dados.", "status" => 404));
                http_response_code(404); exit;

              }

              if(count($parametros) == 0){
                echo json_encode($result);

              } 

              else if(count($result) == 0 || count($parametros) == 0){
                echo json_encode([  ]);

              } 

            


          }


      
       if ($method == "POST") {
       $req = json_decode(file_get_contents('php://input'), true);
         //var_dump(array_values($data));
        // var_dump($data['sigla']);
       $db = new DB();

       $conexao = $db ->connect();
       // validação verifica se sigla já existe
        $rsSigla =  $conexao->prepare("select * from tb_uf WHERE SIGLA= :sigla");
         $rsSigla->execute(array(
        'sigla' => $req['sigla']
      ));
         $resultSigla =  $rsSigla-> fetchAll();
         //var_dump($resultSigla);

         if (count($resultSigla)> 0) {
          echo json_encode(array("mensagem" => "Não foi possível incluir uf no banco, pois já existe uma uf com essa mesma sigla", "status" => 404));
          http_response_code(404); exit;

        }
         // validação verifica se nome já existe
            $rsNome =  $conexao->prepare("select * from tb_uf WHERE NOME= :nome");
         $rsNome->execute(array(
        'nome' => $req['nome']
      ));
         $resultNome =  $rsNome-> fetchAll();
         //var_dump($resultSigla);

         if (count($resultNome)> 0) {
          echo json_encode(array("mensagem" => "Não foi possível incluir uf no banco, pois já existe uma uf com esse mesmo nome", "status" => 404));
          http_response_code(404); exit;

        }

        if (is_null($req['sigla'])) {
           echo json_encode(array("mensagem" => "Não foi possível incluir uf no banco, pois o campo sigla é obrigatório", "status" => 404,   "nomeDoCampo" =>"sigla"));
          http_response_code(404); exit;
         
        }
          if (is_null($req['nome'])) {
           echo json_encode(array("mensagem" => "Não foi possível incluir uf no banco, pois o campo nome é obrigatório", "status" => 404,   "nomeDoCampo" =>"nome"));
          http_response_code(404); exit;
         
        }
          if (is_null($req['status'])) {
           echo json_encode(array("mensagem" => "Não foi possível incluir uf no banco, pois o campo status é obrigatório", "status" => 404,   "nomeDoCampo" =>"status"));
          http_response_code(404); exit;
         
        }


       $rs =  $conexao->prepare("INSERT INTO tb_uf (codigo_uf, sigla, nome, status) Values (nextval('SEQUENCE_UF'), :sigla, :nome, :status)");
       $exec =  $rs->execute($req);
           // echo $rs->rowCount();
      


       if($exec){
         $rs =  $conexao->prepare("SELECT CODIGO_UF, SIGLA, NOME, STATUS FROM TB_UF ORDER BY CODIGO_UF DESC ");
         $rs->execute();
         $obj =  $rs-> fetchAll(PDO::FETCH_ASSOC);
         $result = array_map(function ($req) {
          return [     
            'codigoUF'       => $req['codigo_uf'],
            'sigla'     => $req['sigla'],
            'nome' => $req['nome'],
            'status'    => $req['status'],
          ];
        }, $obj);
         echo json_encode($result);

       } else{
        echo json_encode(array("mensagem" => "Não foi possível incluir UF no banco de dados.", "status" => 404));
        http_response_code(404); exit;


      }
    }

    if ($method == "PUT") {
     $req = json_decode(file_get_contents('php://input'), true);
           //var_dump(array_values($req));
          // var_dump($data['sigla']);
     $db = new DB();

     $conexao = $db ->connect();

      $rsSigla =  $conexao->prepare("select * from tb_uf WHERE SIGLA= :sigla");
         $rsSigla->execute(array(
        'sigla' => $req['sigla']
      ));
         $resultSigla =  $rsSigla-> fetchAll();
         //var_dump($resultSigla[0]['codigo_uf']);
          if (is_null($req['codigoUF'])) {
           echo json_encode(array("mensagem" => "Não foi possível atualizar uf no banco, pois o campo codigoUF é obrigatório", "status" => 404,   "nomeDoCampo" =>"codigoUF"));
          http_response_code(404); exit;
         
        }
        if (is_null($req['sigla'])) {
           echo json_encode(array("mensagem" => "Não foi possível atualizar uf no banco, pois o campo sigla é obrigatório", "status" => 404,   "nomeDoCampo" =>"sigla"));
          http_response_code(404); exit;
         
        }
          if (is_null($req['nome'])) {
           echo json_encode(array("mensagem" => "Não foi possível atualizar uf no banco, pois o campo nome é obrigatório", "status" => 404,   "nomeDoCampo" =>"nome"));
          http_response_code(404); exit;
         
        }
          if (is_null($req['status'])) {
           echo json_encode(array("mensagem" => "Não foi possível atualizar uf no banco, pois o campo status é obrigatório", "status" => 404,   "nomeDoCampo" =>"status"));
          http_response_code(404); exit;
         
        }




         if ((count($resultSigla)> 0) && 
                $resultSigla[0]['codigo_uf'] != $req['codigoUF']) {
          echo json_encode(array("mensagem" => "Não foi possível atualizar uf no banco, pois já existe uma uf com essa mesma sigla", "status" => 404));
          http_response_code(404); exit;

        }
            $rsNome =  $conexao->prepare("select * from tb_uf WHERE NOME= :nome");
         $rsNome->execute(array(
        'nome' => $req['nome']
      ));
         $resultNome =  $rsNome-> fetchAll();
         //var_dump($resultSigla);

         if (count($resultNome)> 0 && 
                $resultNome[0]['codigo_uf'] != $req['codigoUF']) {
          echo json_encode(array("mensagem" => "Não foi possível atualizar uf no banco, pois já existe uma uf com esse mesmo nome", "status" => 404));
          http_response_code(404); exit;

        }
            
     $rs =  $conexao->prepare("UPDATE tb_uf SET sigla = :sigla, nome = :nome, status = :status WHERE codigo_uf = :codigoUF");
     $exec =  $rs->execute($req);

     $error= $rs->errorInfo();
        //var_dump($error);
        //var_dump($rs->debugDumpParams());
     if($exec){
       $rs =  $conexao->prepare("SELECT CODIGO_UF, SIGLA, NOME, STATUS FROM TB_UF ORDER BY CODIGO_UF DESC ");
       $rs->execute();
       $obj =  $rs-> fetchAll(PDO::FETCH_ASSOC);
       $result = array_map(function ($req) {
        return [     
          'codigoUF'       => $req['codigo_uf'],
          'sigla'     => $req['sigla'],
          'nome' => $req['nome'],
          'status'    => $req['status'],
        ];
      }, $obj);
       echo json_encode($result);

     } else{
      echo json_encode(array("mensagem" => "Não foi possível alterar UF no banco de dados.", "status" => 404));
      http_response_code(404); exit;


    }


  }

    if ($method == "DELETE" && $param != '') {
      $db = new DB();

      $conexao = $db ->connect();
      $rs =  $conexao->prepare("select * from tb_uf where codigo_uf = :codigoUF");
      $rs->execute(array(
        'codigoUF' => $param
      ));
      $verificaUF =  $rs-> fetchAll(PDO::FETCH_ASSOC);
           //var_dump($verificaUF);

      if (count($verificaUF) > 0) {
        $rs =  $conexao->prepare("UPDATE tb_uf set status = 2 WHERE codigo_uf = :codigoUF");
        $exec =  $rs->execute(array(
          'codigoUF' => $param
        ));
            if($exec){
         $rs =  $conexao->prepare("SELECT CODIGO_UF, SIGLA, NOME, STATUS FROM TB_UF ORDER BY CODIGO_UF DESC ");
         $rs->execute();
         $obj =  $rs-> fetchAll(PDO::FETCH_ASSOC);
         $result = array_map(function ($req) {
          return [     
            'codigoUF'       => $req['codigo_uf'],
            'sigla'     => $req['sigla'],
            'nome' => $req['nome'],
            'status'    => $req['status'],
          ];
        }, $obj);
         echo json_encode($result);

       } else{
        echo json_encode(array("mensagem" => "Não foi possível deletar UF no banco de dados.", "status" => 404));
        http_response_code(404); exit;


      }


      } else{
        echo json_encode(array("mensagem" => "Não foi possível encontrar uma uf com o código informado.", "status" => 404));
        http_response_code(404); exit;
      }


    }
  }


  }



      function gerarSQLConsultarListar($params){
      	$parametros  = [];
      	$sql = 'SELECT CODIGO_UF, SIGLA, NOME, STATUS FROM TB_UF WHERE 1 = 1 ';
      	unset($params['path']);
        //print_r($params);

      	if(!is_null($params['codigoUF'])){
      		$sql .= ' AND CODIGO_UF = :codigoUF ';
      	}

      	if(!is_null($params['sigla']))
      	{
      		$sql .= " AND SIGLA = :sigla ";

      	}
      	if(!is_null($params['nome']))
      	{
      		$sql .= ' AND nome = :nome ';
      	}
      	if(!is_null($params['status']))
      	{
      		$sql .= "AND STATUS = :status ";

      	}
      	$sql .= " ORDER BY CODIGO_UF DESC ";
      	return [$sql, $params];   
      }



    ?> 