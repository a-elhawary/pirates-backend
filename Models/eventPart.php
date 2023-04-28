
<?php 
     class eventPart extends Model{
        public function __construct(){
            parent::__construct("eventparticipators",array("Email", "EventID", "Status", "university_id_img", "national_id_img"));
        }
    }
 ?>
