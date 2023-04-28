<?php
class Connection{
				private static $pdo;
				// make constructor private for singleton pattern
				private function __construct(){}
				// get a new connection
				public static function getConnection(){
								if(empty(self::$pdo)){
												// create a new connection
												try{
																self::$pdo = new PDO('mysql:host=localhost;dbname=pirates', "root", "");
																self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
												}catch(PDOException $error){
																echo $error->getMessage();
												}
								}
								return self::$pdo;
				}
}

?>
