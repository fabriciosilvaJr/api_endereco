<?php 

	class Bairros{

		public function listar(){
			try {

				$db = new DB(); 
				parse_str($_SERVER['QUERY_STRING'], $parameters);
				$bairros = new Bairros();
				$recursos = $bairros -> gerarSQLConsultarListar($parameters);
		    $sql = $recursos[0]; //sql
		    $parametros  = $recursos[1]; 
             //var_dump($recursos);
		    $rs = $db ->connect()->prepare($sql);
		    $rs->execute($parametros);
		    $error= $rs->errorInfo();

		    $obj =  $rs-> fetchAll(PDO::FETCH_ASSOC);
		    $result = array_map(function ($data) {
		    	return [     
		    		'codigoBairro'       => $data['codigo_bairro'],
		    		'codigoMunicipio'     => $data['codigo_municipio'],
		    		'nome' => $data['nome'],
		    		'status'    => $data['status'],
		    	];
		    }, $obj);

		    if((count($obj) == 1 && !is_null($parametros['codigoBairro']))) {
		    	if(count($obj) > 0){

		    		$umRegistro = [
		    			'codigoBairro'   => $obj[0]['codigo_bairro'],
		    			'codigoMunicipio'     => $obj[0]['codigo_municipio'],
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

		  } catch (PDOException $e) {
				    //print "Error!: " . $e->getMessage() . "</br>";
		  	echo json_encode(array("mensagem" => "Não foi possível consultar Bairro no banco de dados.", "status" => 404));
		  	http_response_code(404); 


		  }



		}

		public function adicionar(){
			try {

				$req = json_decode(file_get_contents('php://input'), true);
				$db = new DB();
				$conexao = $db ->connect();


				if (is_null($req['codigoMunicipio'])) {
					echo json_encode(array("mensagem" => "Não foi possível incluir bairro no banco, pois o campo codigoMunicipio é obrigatório", "status" => 404,   "nomeDoCampo" =>"codigoMunicipio"));
					http_response_code(404); exit;

				}
				if (is_null($req['nome'])) {
					echo json_encode(array("mensagem" => "Não foi possível incluir bairro no banco, pois o campo nome é obrigatório", "status" => 404,   "nomeDoCampo" =>"nome"));
					http_response_code(404); exit;

				}
				if (is_null($req['status'])) {
					echo json_encode(array("mensagem" => "Não foi possível incluir bairro no banco, pois o campo status é obrigatório", "status" => 404,   "nomeDoCampo" =>"status"));
					http_response_code(404); exit;

				}

				$rsMP =  $conexao->prepare("select * from tb_municipio WHERE codigo_municipio = :codigoMunicipio");
				$rsMP->execute(array(
					'codigoMunicipio' => $req['codigoMunicipio']
				));
				$resultMP =  $rsMP -> fetchAll();

				if (count($resultMP)== 0) {
					echo json_encode(array("mensagem" => "Não foi possível incluir bairro no banco, pois ainda não existe um municipio cadastrado com esse código", "status" => 404));
					http_response_code(404); exit;

				}

				$rs =  $conexao->prepare("INSERT INTO tb_bairro (codigo_bairro,codigo_municipio, nome, status) Values (nextval('sequence_bairro'), :codigoMunicipio, :nome, :status)");


				$exec =  $rs->execute($req);
				$error= $rs->errorInfo();
        	  //var_dump($error);

				if($exec){
					$bairros = new Bairros();
					$bairros ->listar();

				} else{
					echo json_encode(array("mensagem" => "Não foi possível incluir Bairro no banco de dados.", "status" => 404));
					http_response_code(404); exit;


				}
				
			} catch (PDOException $e) {
				  //print "Error!: " . $e->getMessage() . "</br>";
				echo json_encode(array("mensagem" => "Não foi possível incluir Bairro no banco de dados.", "status" => 404));
				http_response_code(404); 
				
			}


		}

		public function alterar(){
			$req = json_decode(file_get_contents('php://input'), true);
			$db = new DB();
			$conexao = $db ->connect();
			if (is_null($req['codigoMunicipio'])) {
				echo json_encode(array("mensagem" => "Não foi possível alterar bairro no banco, pois o campo codigoMunicipio é obrigatório", "status" => 404,   "nomeDoCampo" =>"codigoMunicipio"));
				http_response_code(404); exit;

			}
			if (is_null($req['nome'])) {
				echo json_encode(array("mensagem" => "Não foi possível alterar bairro no banco, pois o campo nome é obrigatório", "status" => 404,   "nomeDoCampo" =>"nome"));
				http_response_code(404); exit;

			}
			if (is_null($req['status'])) {
				echo json_encode(array("mensagem" => "Não foi possível alterar bairro no banco, pois o campo status é obrigatório", "status" => 404,   "nomeDoCampo" =>"status"));
				http_response_code(404); exit;

			}

			$rsMP =  $conexao->prepare("select * from tb_municipio WHERE codigo_municipio = :codigoMunicipio");
			$rsMP->execute(array(
				'codigoMunicipio' => $req['codigoMunicipio']
			));
			$resultMP =  $rsMP -> fetchAll();

			if (count($resultMP)== 0) {
				echo json_encode(array("mensagem" => "Não foi possível alterar bairro no banco, pois ainda não existe um municipio cadastrado com esse código", "status" => 404));
				http_response_code(404); exit;

			}


			$rs =  $conexao->prepare("UPDATE tb_bairro set codigo_municipio = :codigoMunicipio, nome= :nome, status= :status WHERE codigo_bairro = :codigoBairro");
			$exec =  $rs->execute($req);

			$error= $rs->errorInfo();
          //var_dump($error);
          //var_dump($rs->debugDumpParams());
			if($exec){
				$bairros = new Bairros();
				$bairros ->listar();


			} else{
				echo json_encode(array("mensagem" => "Não foi possível alterar bairro no banco de dados.", "status" => 404));
				http_response_code(404); exit;

			}

		}

		public function deletar($param){
			$db = new DB();
			$conexao = $db ->connect();
			$rs =  $conexao->prepare("select * from tb_bairro where codigo_bairro = :codigoBairro");
			$rs->execute(array(
				'codigoBairro' => $param
			));
			$verificaB =  $rs-> fetchAll(PDO::FETCH_ASSOC);
		      //var_dump($verificaMP);
		       //var_dump($verificaUF);

			if (count($verificaB) > 0) {
				$rs =  $conexao->prepare("UPDATE tb_bairro set status = 2 WHERE codigo_bairro = :codigoBairro");
				$exec =  $rs->execute(array(
					'codigoBairro' => $param
				));
				if($exec){
					$bairros = new Bairros();
					$bairros ->listar();


				} else{
					echo json_encode(array("mensagem" => "Não foi possível desativar bairro no banco de dados.", "status" => 404));
					http_response_code(404); exit;


				}


			} else{
				echo json_encode(array("mensagem" => "Não foi possível encontrar um bairro com o código informado.", "status" => 404));
				http_response_code(404); exit;
			}

		}

		function gerarSQLConsultarListar($params){
          $parametros  = [];
          $sql = 'SELECT CODIGO_BAIRRO, CODIGO_MUNICIPIO, NOME, STATUS FROM TB_BAIRRO WHERE 1 = 1 ';
          unset($params['path']);
          //print_r($params);

          if(!is_null($params['codigoBairro'])){
           $sql .= ' AND CODIGO_BAIRRO = :codigoBairro ';
          }

          if(!is_null($params['codigoMunicipio'])){
           $sql .= ' AND CODIGO_MUNICIPIO = :codigoMunicipio ';
          }

          if(!is_null($params['nome']))
          {
           $sql .= ' AND nome = :nome ';
          }
          if(!is_null($params['status']))
          {
           $sql .= "AND STATUS = :status ";

          }
          $sql .= " ORDER BY CODIGO_BAIRRO DESC ";
          return [$sql, $params];   
        }



	}



 ?>