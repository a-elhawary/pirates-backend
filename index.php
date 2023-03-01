<?php
require_once("Helpers/Router.php");
$base = "";
$router = new Router($base);

$router->get("/about", function(){
	echo "<h1>About US</h1>";
});

$router->post("/events/{id}", function($params){
	echo "<h1>Event with ID ".$params["id"];
});

$router->route();
