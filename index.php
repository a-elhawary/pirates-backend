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

$router->post("/register", function(){
	$fields  = array("FirstName", "LastName", "Email", "Password", "Role", "PhoneNumber", "Gender", "DOB", "University", "Faculty");
    $eventModel = new Model("register",$fields);
    $read = $eventModel->getAll();
    
    
    $columnArray  = array_column($read, 'Email');
    
    $emptyFirstName = false;
    $emptyLastName = false;
    $emptyEmail = false;
    $emptyPassword = false;
    $emptyRole = false;
    $emptyPhoneNumber = false;
    $emptyGender = false;
    $emptyDOB = false;
    $emptyUniversity = false;
    $emptyFaculty = false;

    $isValidated = true;
    $Repeat = false;


    if(empty($_POST['FirstName'])){  
        $emptyUser = true;
        $isValidated = false;
        echo "Please enter FirstName!\n";
      }
    if(empty($_POST['LastName'])){
        $emptyPass = true;
        $isValidated = false;
        echo "Please enter LastName!\n";
    }
    if(empty($_POST['Email'])){  
        $emptyUser = true;
        $isValidated = false;
        echo "Please enter Email!\n";
      }
    if(empty($_POST['Password'])){
        $emptyPass = true;
        $isValidated = false;
        echo "Please enter Password!\n";
    }
    if(empty($_POST['Role'])){  
        $emptyUser = true;
        $isValidated = false;
        echo "Please enter your Role!\n";
      }
    if(empty($_POST['PhoneNumber'])){
        $emptyPass = true;
        $isValidated = false;
        echo "Please enter PhoneNumber!\n";
    }
    if(empty($_POST['Gender'])){  
        $emptyUser = true;
        $isValidated = false;
        echo "Please enter your Gender!\n";
      }
    if(empty($_POST['DOB'])){
        $emptyPass = true;
        $isValidated = false;
        echo "Please enter DOB!\n";
    }
    if(empty($_POST['University'])){  
        $emptyUser = true;
        $isValidated = false;
        echo "Please enter your University!\n";
      }
    if(empty($_POST['Faculty'])){
        $emptyPass = true;
        $isValidated = false;
        echo "Please enter Faculty!\n";
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
    $eventModel = new Model("register",$fields);
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
