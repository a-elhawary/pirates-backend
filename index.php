<?php
require_once("Helpers/Router.php");
require_once("Models/Model.php");
require_once("Validator.php");
require_once("Models/users.php");
require_once("Models/event.php");


$base = "";

$router = new Router($base);



$router->get("/about", function(){
	echo "<h1>About US</h1>";
});


$router->post("/events", function(){
	


	$isValid  = validate();
	
	if($isValid){
		$eventModel = new event();
		$eventModel->insert($_POST);
	}



});

$router->post("/register", function(){
    $eventModel = new users();
    $read = $eventModel->getAll();
    
    $columnArray  = array_column($read, 'Email');

    $isValidated = true;
    $Repeat = false;


    foreach ($_POST as $key => $value) {
        if(empty($value)){
            echo "The field ". "(".$key.")"." can't be empty! <br>";
            $isValidated = false;
        }
    }

    if(in_array($_POST['Email'],$columnArray)){
        $Repeat = true;
        $isValidated = false;
        echo "Email already exists!\n";
    }

    if($isValidated && $_SERVER["REQUEST_METHOD"] == "POST"){
        $eventModel->insert($_POST);
        echo "Success!\n";
    }
});

$router->post("/login", function(){
    $fields  = array("FirstName", "LastName", "Email", "Password", "Role", "PhoneNumber", "Gender", "DOB", "University", "Faculty");
    $eventModel = new Model("users",$fields);
    $read = $eventModel->getAll();
    
    $EmailColumn  = array_column($read, 'Email');
    $PassColumn  = array_column($read, 'Password');
    

    
    $emptyEmail = false;
    $emptyPass = false;

    $wrongEmail = false;
    $wrongPass = false;

    $success = false;

    if(empty($_POST['Email'])){  
        $emptyEmail = true;
      }
    elseif(empty($_POST['password'])){
        $emptyPass = true;
    }

    elseif(!in_array($_POST['Email'],$EmailColumn)){
        $wrongEmail = true;
    }
    else{
        if(isset($_POST["submit"])){
            $i =0;
            for($i;$i<count($read);$i++){
                if($read[$i]['Email'] ==$_POST['Email']){
                    if($read[$i]['password']==$_POST['password']){
                        $success = true;
                    }
                    else{
                        $wrongPass = true;
                    }
                }
            }
        }
    }

    if($emptyEmail){
        echo "Please enter Email";
    }
    elseif($emptyPass){
        echo "Please enter a password";
    }
    elseif($wrongEmail){
        echo "No such Emails exists!";
    }
    elseif($wrongPass){
        echo "Incorrect Password!";
    }
    elseif(isset($_POST) && $success){
        echo "Success!";
    }
});

$router->route();

?>
