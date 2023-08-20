<?php 

	if($api == 'uf'){
		if($method == "GET"){
			//db = DB::connect();
			$db = new DB(); 
		    $rs = $db ->connect()->prepare("SELECT * FROM tb_uf");
			 $rs->execute();
			$obj =  $rs-> fetchAll();

			 var_dump($obj);
		}
	}



 ?> 