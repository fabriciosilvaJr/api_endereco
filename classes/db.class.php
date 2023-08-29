<?php 


class DB {
	public static function connect(){
		
			$host = 'silly.db.elephantsql.com';
			$user = 'jozbdoyt';
			$pass = 'FyqQtiVxGYO81KVV4ODkqBhxFTlqBALJ';
			$base = 'jozbdoyt';
			$pdo = new PDO("pgsql:host=$host;port=5432;dbname=$base;user=$user;password=$pass");
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			 return $pdo;



	}

	

}



?>