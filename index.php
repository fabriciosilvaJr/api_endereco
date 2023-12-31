<?php 
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
	header("HTTP/1.1 200 OK");
	die();
}


	//date_default_timezone_set("America/Sao_Paulo");

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
$rota->add('GET', '/municipio', 'Municipios::listar', true);
$rota->add('POST', '/municipio', 'Municipios::adicionar', true);
$rota->add('PUT', '/municipio', 'Municipios::alterar', true);
$rota -> add('DELETE', '/municipio/[PARAM]', 'Municipios::deletar', true);
$rota->add('GET', '/bairro', 'Bairros::listar', true);
$rota->add('POST', '/bairro', 'Bairros::adicionar', true);
$rota->add('PUT', '/bairro', 'Bairros::alterar', true);
$rota -> add('DELETE', '/bairro/[PARAM]', 'Bairros::deletar', true);
$rota->add('GET', '/pessoa', 'Pessoas::listar', true);
$rota->add('POST', '/pessoa', 'Pessoas::adicionar', true);
$rota->add('PUT', '/pessoa', 'Pessoas::alterar', true);
$rota -> add('DELETE', '/pessoa/[PARAM]', 'Pessoas::deletar', true);




$rota -> ir($_GET['path']);



?>