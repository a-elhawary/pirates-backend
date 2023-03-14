<?php
require_once("Helpers/Router.php");
require_once("Helpers/helpers.php");
require_once("Models/Model.php");
require_once("Validator.php");
require_once("Models/users.php");
require_once("Models/event.php");

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

if(file_get_contents('php://input') != NULL){
$_POST = json_decode(file_get_contents('php://input'));
$_POST = convert_object_to_array($_POST);
}




$base = "";

$router = new Router($base);



$router->get("/about", function(){
	echo "<h1>About US</h1>";
});


$router->post("/events", function(){
    validateEvent();
});

$router->post("/register", function(){
    validateRegister();
});

$router->post("/login", function(){
    validateLogin();
});

$router->route();

?>
