<?php 


if($api == 'uf'){
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

          //var_dump(count($obj));
          // if (count($obj) == 1 && is_null($parametros['status'])) {
          // 	echo "teste";
          // }


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

   


          if(count($parametros) == 0){
          	echo json_encode($result);

          } else if(count($result) == 0 || count($parametros) == 0){
          	echo json_encode([  ]);

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