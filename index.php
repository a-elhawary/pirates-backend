<?php
require_once("Helpers/Router.php");
require_once("Helpers/helpers.php");
require_once("Models/Model.php");
require_once("Models/slots.php");
require_once("Validator.php");
require_once("Models/users.php");
require_once("Models/event.php");
require_once("Models/eventPart.php");

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

$_POST = json_decode(file_get_contents('php://input'));
$_POST = convert_object_to_array($_POST);





$base = "";

$router = new Router($base);


$router->get("/about", function(){
	echo "<h1>About US</h1>";
});

$router->get("/event/{event}", function($args){
	$eventModel = new event();
    echo json_encode($eventModel->getBy("Name",str_replace("%20"," ",$args["event"])));
});

$router->post("/addevents", function(){
    validateEvent();
});


$router->get("/events", function(){
    $eventModel = new event();
    echo json_encode($eventModel->getAll());

});

$router->post("/register", function(){
    
    validateRegister();
});

$router->post("/login", function(){
    validateLogin();
});

$router->post("/AddInterviewSlot", function(){
    validateSlot();
});

$router->post("/EventRegistration", function(){
    $eventModel = new eventPart();
    $read = $eventModel->getAll();
    $EmailColumn = array_column($read, 'Email');
    $EventColumn = array_column($read, 'EventID');

    //var_dump($_POST);
    // $email = $_POST['email'];
    // $eventID = $_POST['eventID'];
    $files = ['imageUni', 'imageNat'];
    UploadEventRegister("uploads/", $files);
});

$router->route();

?>
