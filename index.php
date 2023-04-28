<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

require_once("Helpers/Router.php");
require_once("Helpers/helpers.php");
require_once("Models/Model.php");
require_once("Models/slots.php");
require_once("Validator.php");
require_once("Models/users.php");
require_once("Models/event.php");
require_once("Models/eventPart.php");



$base = "/pirates-backend";

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
    $_POST = json_decode(file_get_contents('php://input'));
    $_POST = convert_object_to_array($_POST);
    validateRegister();
});

$router->post("/login", function(){
    $_POST = json_decode(file_get_contents('php://input'));
    $_POST = convert_object_to_array($_POST);
    validateLogin();
});

$router->post("/auth", function(){
    $_POST = json_decode(file_get_contents('php://input'));
    $_POST = convert_object_to_array($_POST);
    $response = [];
    $response["isAdmin"] = false;
    if(empty($_POST["token"])){
        echo json_encode($response);
        die();
    }
    $user = new users();
    $token = json_decode($_POST['token']);
    $userData = $user->getBy("id", $token['id']);
    if(count($userData) < 0){
        echo json_encode($response);
        die();
    }
    if($userData[0]["Role"] == "admin"){
        $response["isAdmin"] = true;
        echo json_encode($response);
    }
});

$router->post("/AddInterviewSlot", function(){
    $_POST = json_decode(file_get_contents('php://input'));
    $_POST = convert_object_to_array($_POST);
    validateSlot();
});

$router->post("/EventRegistration", function(){
    $files = ['imageUni', 'imageNat'];
    UploadEventRegister(__DIR__."/uploads/", $files);
});

$router->get("/event/slotDays/{eventID}", function($args){
    $_POST = json_decode(file_get_contents('php://input'));
    $_POST = convert_object_to_array($_POST);
    $data = [];
    array_push($data, $args["eventID"]);
    $slots = new slots();
    $data = $slots->runPreparedStatement("SELECT DISTINCT `Date` FROM slots WHERE EventID = ? ORDER BY `Date` ASC", $data);
    echo json_encode($data);
});

$router->get("/event/slots/{eventID}/{date}", function($args){
    $data = [];
    array_push($data, $args["date"]);
    array_push($data, $args["eventID"]);
    $slots = new slots();
    $data = $slots->runPreparedStatement("SELECT * FROM slots WHERE `Date` = ? AND EventID = ? ORDER BY StartTime ASC", $data);
    echo json_encode($data);
});

$router->get("/slotDays/{userId}", function($args){
    $user = new Users();
    $userData = $user->getBy("id", $args["userId"]);
    if(count($userData) == 0) die();
    $data = [];
    array_push($data, $userData[0]["Email"]);
    $slots = new slots();
    $data = $slots->runPreparedStatement("SELECT DISTINCT `Date` FROM slots WHERE AdminEmail = ? ORDER BY `Date` ASC", $data);
    echo json_encode($data);
});

$router->get("/slots/{userId}/{date}", function($args){
    $data = [];
    array_push($data, $args["date"]);
    $user = new Users();
    $userData = $user->getBy("id", $args["userId"]);
    if(count($userData) == 0) die();
    array_push($data, $userData[0]["Email"]);
    $slots = new slots();
    $data = $slots->runPreparedStatement("SELECT * FROM slots WHERE `Date` = ? AND AdminEmail = ? ORDER BY StartTime ASC", $data);
    echo json_encode($data);
});

$router->get("/takeSlot/{slotID}/{userID}", function($args){
    $response = [];
    $response["took"] = false;
    $user = new Users();
    $userData = $user->getBy("id", $args["userID"]);
    if(count($userData) == 0) die();
    $slots = new slots();
    $slotsData = $slots->getBy("SlotID", $args["slotID"]);
    if(count($slotsData) == 0) die();
    if($slotsData[0]["StudentEmail"] == null){
        $slots->update("StudentEmail", $userData[0]["Email"], "SlotID", $args["slotID"]);
        $response["took"] = true;
    }
    echo json_encode($response);
});

$router->get("/isregistered/{eventID}/{userID}", function ($args){
    $response = [];
    $response["isRegistered"] = false;
    $user = new Users();
    $userData = $user->getBy("id", $args["userID"]);
    if(count($userData) == 0) die();
    $eventPart = new eventPart();
    $eventPartData = $eventPart->getBy("Email", $userData[0]["Email"]);
    for($i = 0; $i < count($eventPartData); $i++){
        if($eventPartData[$i]["EventID"] == $args["eventID"]){
            $response["isRegistered"] = true;
            echo json_encode($response);
            die();
        }
    }
    echo json_encode($response);
});
$router->get("/hasInterview/{eventID}/{userID}", function ($args){
    $response = [];
    $response["has"] = false;
    $user = new Users();
    $userData = $user->getBy("id", $args["userID"]);
    if(count($userData) == 0) die();
    $slots = new slots();
    $slotsData = $slots->getBy("StudentEmail", $userData[0]["Email"]);
    for($i = 0; $i < count($slotsData); $i++){
        if($slotsData[$i]["EventID"] == $args["eventID"]){
            $response["has"] = true;
            echo json_encode($response);
            die();
        }
    }
    echo json_encode($response);
});

$router->route();

?>
