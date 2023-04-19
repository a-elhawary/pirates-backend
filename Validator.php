<?php


function isValidDateForm(string $date, string $format = 'Y-m-d H:i:s'): bool
{
    $dateObj = DateTime::createFromFormat($format, $date);
    return $dateObj && $dateObj->format($format) == $date;
}

function isEmpty(){
    $isValidated = true;
    foreach ($_POST as $key => $value) {
        if(empty($value)){
            echo "The field ". "(".$key.")"." can't be empty! <br>";
        }
    }
    return $isValidated;
}

function isValidDateEvent(){
    $isValidated = true;
    $date_now = date("Y-m-d\TH:i");
    $eventDate = $_POST["Date"];
    if(isValidDateForm($eventDate,"Y-m-d\TH:i")){ // check if the time entered is valid 
        $date_now = date("Y-m-d\TH:i:s");
        if ($date_now >= $eventDate) { //check if the time entered is in the future
            echo "The Date entered have already passed!";
            $isValidated = false;
        }
    }
    else{
        echo "Date format entered is false!";
        $isValidated = false;
    }
    return $isValidated;

}

function isValidDateBirth(){
    $isValidated = true;

    $eventDate = $_POST["DOB"];
    if(isValidDateForm($eventDate,"Y-m-d")){ // check if the time entered is valid 
        $date_now = date("Y-m-d");
        if ($date_now < $eventDate) { //check if the time entered is in the future
            echo "You're born in the future?";
            $isValidated = false;
        }
    }
    else{
        echo "Date format entered is false!";
        $isValidated = false;
    }
    return $isValidated;

}


function validateEvent(){

    $isValidated = true;
    $eventModel = new event();
    
    $isValidated = isEmpty();
    $isValidated = isValidDateEvent();

    if($isValidated){
        $eventModel->insert($_POST);
    }

 }
 function validateRegister(){
    $_POST['Role'] = "user";
    unset($_POST['ConfirmPassword']);
    $UserModel = new users();
    $read = $UserModel->getAll();
    $columnArray  = array_column($read, 'Email');


    $isValidated = true;

    $isValidated = isEmpty();
    $isValidated = isValidDateBirth();

    if(in_array($_POST['Email'],$columnArray)){

        $isValidated = false;
        echo "Email already exists!\n";
    }

    if($isValidated){
        $UserModel->insert($_POST);
    }

 }

 function validateLogin(){

    $isValidated = true;

    $UserModel = new users();
    $read = $UserModel->getAll();
    $EmailColumn  = array_column($read, 'Email');
    
   
    $isValidated = isEmpty();

    if(!in_array($_POST['Email'],$EmailColumn) && $isValidated){
        echo "Email doesn't exist!";
    }
    elseif($isValidated){
            $i =0;
            for($i;$i<count($read);$i++){
                if($read[$i]['Email'] ==$_POST['Email']){
                    if($read[$i]['Password']==$_POST['Password']){
                        echo "Success";
                    }
                    else{
                        echo "Wrong Password";
                    }
                }
            }

    }
 }

 function validateSlot(){
    $isValidated = true;

    $SlotModel = new slots();
    $read = $SlotModel->getBy("AdminEmail",$_POST['AdminEmail']);

    $isValidated = isEmpty();
    if(count($read)!=0  && $isValidated ){
        for($i;$i<count($read);$i++){
            if(strcmp($read[$i]['Date'],$_POST['Date']) == 0 &&
            strcmp(substr($read[$i]['StartTime'],0,-3),$_POST['StartTime']) == 0  &&
            strcmp(substr($read[$i]['EndTime'],0,-3),$_POST['EndTime']) == 0 ){
                        $isValidated = false;
                    }
            }
    }
    if($isValidated){
        $SlotModel->insert($_POST);
    }
}

function validateFileName($filename, $desiredContent) {
    return ((bool) ((preg_match($desiredContent, $filename)) ? true : false) && (bool) ((mb_strlen($filename,"UTF-8") <= 200) ? true : false));
}
function validateFileType($fileType, $allowedTypes){
    return in_array($fileType, $allowedTypes);
}
function UploadEventRegister($dir,$files){
    $target_dir = $dir;
    $files = $files;
    $success = true;
    $message = '';
    $validFiles = array();
    foreach ($files as $file) {
        $desiredContent = "`^[-0-9a-zA-Z_\. ()]+$`i";
        $validName = validateFileName($_FILES[$file]['name'],$desiredContent);
        $allowedTypes = array('image/jpeg', 'image/png', 'image/jpg');
        $validType = validateFileType($_FILES[$file]['type'], $allowedTypes);
        if ($validName && $validType) {
            array_push($validFiles, $file);
        } else {
            if (!$validName){
                $message .= 'Error uploading file ' . $_FILES[$file]['name'] . '. Invalid Name or more than 200 characters.' . '<br>';
            }
            if (!$validType){
                $message .= 'Error uploading file ' . $_FILES[$file]['name'] . '. Invalid Type, please upload jpg/jpeg/png photos.' . '<br>';
            }
        }
    }
    if (count($validFiles) == count($files)) {
        foreach ($validFiles as $file) {
            $unique_id = time().mt_rand();
            $target_file = $target_dir . $unique_id . '_' . basename($_FILES[$file]['name']);
            $uploaded = move_uploaded_file($_FILES[$file]['tmp_name'], $target_file);
            if ($uploaded) {
                $message .= 'File ' . $_FILES[$file]['name'] . ' uploaded successfully.' . '<br>';
            } else {
                $message .= 'Error uploading file ' . $_FILES[$file]['name'] . '.<br>';
            }
        }
    } else {
        $success = false;
    }
    $response = array('success' => $success, 'message' => $message);
    echo json_encode($response);
}

?>