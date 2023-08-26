<?php 
    header('Access-Control-Allow-Origin: *');
    header('Content-type: application/json');


	date_default_timezone_set("America/Sao_Paulo");

	if(isset($_GET['path']))  {$path = explode("/", $_GET['path']);} else {echo "Caminho não existe"; exit;}

	if(isset($path[0])) {$api = $path[0];} else {echo "Caminho não existe"; exit;}
	if(isset($path[1])) {$param = $path[1];} else { $param = '';}

	$method = $_SERVER['REQUEST_METHOD'];
	
	$GLOBALS['secretJWT'] = '123456';



    include_once "classes/db.class.php";
    include_once "classes/jwt.class.php";
    include_once "classes/pessoas.class.php";
	include_once "api/uf/uf.php";
	include_once "api/pessoa/pessoa.php";





 ?>