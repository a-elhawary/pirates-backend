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

    $desiredContent = "`^[-0-9a-zA-Z_\. ()]+$`i";
    $validName = validateFileName($_FILES['Image']['name'],$desiredContent);
    $allowedTypes = array('image/jpeg', 'image/png', 'image/jpg');
    $validType = validateFileType($_FILES['Image']['type'], $allowedTypes);

    if($isValidated && $validName && $validType){
        $unique_id = time().mt_rand();
        $target_file = __DIR__.'/uploads/events/'. $unique_id . '_' . basename($_FILES['Image']['name']);
        $uploaded = move_uploaded_file($_FILES["Image"]['tmp_name'], $target_file);
        $_POST["Image"] = substr($target_file, strlen(__DIR__.""));
        $eventModel->insert($_POST);
    }
 }
 function validateRegister(){
    $_POST['Role'] = "user";
    $_POST['Password'] = password_hash($_POST["Password"], PASSWORD_BCRYPT);
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

    $response = [];
    $response["success"] = false;
    $response["message"] = "Login Successful";

    $UserModel = new users();
    $read = $UserModel->getBy("Email", $_POST["Email"]);

    if(count($read) <= 0){
        $response["message"] = "Email doesn't exist!";
    }
    else{
            $i =0;
            for($i;$i<count($read);$i++){
                if($read[$i]['Email'] ==$_POST['Email']){
                    if(password_verify($_POST['Password'],$read[$i]['Password'])){
                        $response["success"] = true;
                        $response["token"] = [];
                        $response["token"]["id"] = $read[$i]["id"];
                        $response["token"]["email"] = $read[$i]["Email"];
                        $response["token"]["role"] = $read[$i]["Role"];
                    }
                    else{
                        $response["message"] = "Wrong Password";
                    }
                }
            }

    }
    echo json_encode($response);
 }

 function addSingleSlot($data){
    $SlotModel = new slots();
    $read = $SlotModel->getBy("AdminEmail",$data["AdminEmail"]);

    $isValidated = isEmpty();
    if(count($read)!=0  && $isValidated ){
        for($i;$i<count($read);$i++){
            if(strcmp($read[$i]['Date'],$data['Date']) == 0 &&
            strcmp(substr($read[$i]['StartTime'],0,-3),$data['StartTime']) == 0  &&
            strcmp(substr($read[$i]['EndTime'],0,-3),$data['EndTime']) == 0 ){
                        $isValidated = false;
                    }
            }
    }
    if($isValidated){
        $SlotModel->insert($data);
    }
 }

 function timeToIntArr($time){
    $timeStrings = explode(":",$time);
    $timeArr = [];
    for($i = 0; $i < count($timeStrings); $i++){
        array_push($timeArr, intval($timeStrings[$i]));
    }
    return $timeArr;
 }

 function addMinsToIntArr(&$timeArr, $mins){
    $timeArr[1] += $mins;
    while($timeArr[1] > 59){
        $timeArr[1] -= 60;
        $timeArr[0] += 1;
    }
 }

 function IntArrToTime($timeArr){
    return $timeArr[0].":".$timeArr[1];
 }

 function validateSlot(){
    $isValidated = true;

    $userModel = new users();
    $userData = $userModel->getBy("id", $_POST["AdminID"]);
    if(count($userData) == 0){
        echo "no user data";
        die();
    }
    $data = [];
    $data["AdminEmail"] = $userData[0]["Email"];
    $data["EventID"] = $_POST["EventID"];
    $data["Date"] = $_POST["Date"];
    $currentTime = timeToIntArr($_POST["StartTime"]);
    $stopTime = timeToIntArr($_POST["EndTime"]);
    $count = 0;
    while($currentTime[0] < $stopTime[0] || ($currentTime[0] == $stopTime[0] && $currentTime[1] < $stopTime[1])){
        $data["StartTime"] = IntArrToTime($currentTime);
        addMinsToIntArr($currentTime, $_POST["InterviewTime"]);
        $data["EndTime"] = IntArrToTime($currentTime);
        addSingleSlot($data);
        $count++;
    }
    $repsonse = [];
    $response["count"] = $count;
    echo json_encode($response);
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
        $data = [];
        $data["Email"] = $_POST["email"];
        $data["EventID"] = $_POST["eventID"];
        $data["Status"] = 0;
        $i = 0;
        foreach ($validFiles as $file) {
            $unique_id = time().mt_rand();
            $target_file = $target_dir . $unique_id . '_' . basename($_FILES[$file]['name']);
            $uploaded = move_uploaded_file($_FILES[$file]['tmp_name'], $target_file);
            if($i == 0){
                $data["university_id_img"]= substr($target_file, strlen(__DIR__.""));
            }else{
                $data["national_id_img"]= substr($target_file, strlen(__DIR__.""));
            }
            if ($uploaded) {
                $message .= 'File ' . $_FILES[$file]['name'] . ' uploaded successfully.' . '<br>';
            } else {
                $message .= 'Error uploading file ' . $_FILES[$file]['name'] . '.<br>';
            }
            $i++;
        }
        $eventPart = new eventPart();
        $eventPart->insert($data);
    } else {
        $success = false;
    }
    $response = array('success' => $success, 'message' => $message);
    echo json_encode($response);
}

?>