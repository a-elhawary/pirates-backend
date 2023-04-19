
<?php 
     class eventPart extends Model{
        public function __construct(){
            parent::__construct("eventparticipators",array("Email", "EventID", "uniPhoto", "natPhoto"));
        }
    }
 ?>
