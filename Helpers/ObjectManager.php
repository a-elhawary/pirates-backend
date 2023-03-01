<?php
class ObjectManager {
    public static $base;

    public static function getBase(){
        return self::$base;
    }

    public static function getImage($file){
        return self::$base."/Views/Images/".$file;
    }

    public static function getTemplate($file){
        return self::$base."/Views/Templates/".$file;
    }

    public static function getUpload($file){
        return self::$base."/Views/Uploads/".$file;
    }

    public static function getPage($file){
        return self::$base."/Views/Pages/".$file;
    }
    
    public static function getCSS($file){
        return self::$base."/Views/Styles/".$file;
    }

    public static function getJS($file){
        return self::$base."/Views/Scripts/".$file;
    }
}
?>