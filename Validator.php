<?php




/**
 * Validates date(or date time) format
 * @param $date
 * @param string $format
 * @return bool
 */
function isValidDate(string $date, string $format = 'Y-m-d H:i:s'): bool
{
    $dateObj = DateTime::createFromFormat($format, $date);
    return $dateObj && $dateObj->format($format) == $date;
}

function validate(){
    

    $isValidated = true;

    //check if there is any empty field
    foreach ($_POST as $key => $value) {
        if(empty($value)){
            echo "The field ". "(".$key.")"." can't be empty! <br>";
            $isValidated = false;
        }
    }

    $eventDate = $_POST["Date"];
    if(isValidDate($eventDate)){ // check if the time entered is valid 

        $date_now = date("Y-m-d H:i:s");
        if ($date_now < $eventDate) { //check if the time entered is in the future
            // do Nothing
        }else{
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


?>