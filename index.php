<?php 
    header('Access-Control-Allow-Origin: *');
    header('Content-type: application/json');


	date_default_timezone_set("America/Sao_Paulo");

	// if(isset($_GET['path']))  {$path = explode("/", $_GET['path']);} else {echo "Caminho não existe"; exit;}

	// if(isset($path[0])) {$api = $path[0];} else {echo "Caminho não existe"; exit;}
	// if(isset($path[1])) {$param = $path[1];} else { $param = '';}

	$method = $_SERVER['REQUEST_METHOD'];
	
	$GLOBALS['secretJWT'] = '123456';

	include_once "classes/autoload.class.php";

	new Autoload();
	$rota = new Rotas();


	$rota->add('POST', '/pessoa/login', 'Pessoas::login', false);
	$rota->add('GET', '/uf', 'UF::listar', true);
	$rota -> add('POST', '/uf' , 'UF::adicionar',true);
	$rota -> add('PUT', '/uf' , 'UF::alterar',true);
	$rota -> add('DELETE', '/uf/[PARAM]', 'UF::deletar', true);
	$rota -> ir($_GET['path']);





 ?>