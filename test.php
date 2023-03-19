<?php
    $error = "";

    function insert(&$error){
        if(empty($_POST["Name"])){
            $error = "Please Enter the Event Name";
            return;
        }
        if(empty($_POST["Description"])){
            $error = "Please Enter the event Description";
            return;
        }
        if(empty($_POST["Location"])){
            $error = "Please Add the Location";
            return;
        }
        if (empty($_POST["Image"])) {
            $error = "Please Add Image for the Event";
            return;
        }
        if (empty($_POST["Date"])) {
            $error = "Please Choose The Events' Date ";
            return;
        }
        $connection = new PDO("mysql:host=localhost;dbname=pirates", "root", "");
        $sql = "INSERT INTO events (Name, Description, Location, Image, Date, isShown, isAdmitting) VALUES ( :Name, :Description, :Location, :Image, :Date, :isShown, :isAdmitting)";
        $stmt = $connection->prepare($sql);
        unset($_POST["submit"]);
        $stmt->execute($_POST);
    }
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])){
        insert($error);
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Pirates Add Events</title>
        <link rel="stylesheet" type="text/css" href="AddEvents.css">
    </head>
    <body>
        <form method="POST">
            <label>Event Name</label>
            <input type="text" name="Name" value="<?php if(!empty($_POST["Name"])){ echo($_POST["Name"]);}?>">
            <label>Event Description</label>
            <input type="text" name="Description" value="<?php if(!empty($_POST["Description"])){ echo($_POST["Description"]);}?>">
            <label>Event Loacation</label>
            <input type="text" name="Location" value="<?php if(!empty($_POST["Location"])){ echo($_POST["Location"]);}?>">
            <label>Event Image URL</label>
            <input type="url" name="Image" value="<?php if(!empty($_POST["Image"])){ echo($_POST["Image"]);}?>">
            <label>Event Date</label>
            <input type="date" name="Date" value="<?php if(!empty($_POST["Date"])){ echo($_POST["Date"]);}?>">
            <label>Show The Event Now?</label>
            <input type="checkbox" name="isShown" value="<?php if(!empty($_POST["isShown"])){ echo($_POST["isShown"]);}?>">
            <label>Can Students Apply Now?</label>
            <input type="checkbox" name="isAdmitting" value="<?php if(!empty($_POST["isAdmitting"])){ echo($_POST["isAdmitting"]);}?>">
            <span><?php echo $error?></span>
            <input class="submit" type="submit" name="submit" value="Submit">
        </form>
    </body>    
</html>