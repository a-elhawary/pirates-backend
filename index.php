<?php
require_once("Helpers/Router.php");
require_once("Models/Model.php");
require_once("Validator.php");


$base = "";

$router = new Router($base);



$router->get("/about", function(){
	echo "<h1>About US</h1>";
});


$router->post("/events", function(){
	


	$isValid  = validate();
	
	if($isValid){
		$fields  = array("ID", "Name", "Description","Location","Image","Date","isShown","isAdmitting");
		$eventModel = new Model("events",$fields);
		$eventModel->insert($_POST);
	}



});

$router->route();

?>
