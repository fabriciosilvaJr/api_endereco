<?php 

	class DB {
		public static function connect(){


			$host = 'silly.db.elephantsql.com';
			$user = 'jozbdoyt';
			$pass = 'FyqQtiVxGYO81KVV4ODkqBhxFTlqBALJ';
			$base = 'jozbdoyt';

			return new PDO("pgsql:host=$host;port=5432;dbname=$base;user=$user;password=$pass");
		}

	



	}



 ?>