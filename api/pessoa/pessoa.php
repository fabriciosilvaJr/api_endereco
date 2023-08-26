	<?php 
	if ($api == 'pessoa') {
		if ($method == "POST") {
			if($param == 'login'){
                $req = json_decode(file_get_contents('php://input'), true);
				Pessoas::login($req['login'], $req['senha']);
				 // $pessoas = new Pessoas(); 
				 //  $pessoas ->login($_POST['login'], $_POST['senha']);
				 //exit;

				
			}
		}
	}




?>