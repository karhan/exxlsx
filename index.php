<?php
/**
 * @author Dimous <kasimowsky@gmail.com>
 * @copyright (c) 2016, Dimous
 */
 
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('Europe/London');

set_include_path(get_include_path() . PATH_SEPARATOR . realpath("."));
//---

spl_autoload_register();
//---
  

$oApplication = new Application(parse_ini_file("routes.ini", TRUE), parse_ini_file("settings.ini", TRUE));
//---

 

try {
    echo $oApplication->run(filter_input(INPUT_GET, "request", FILTER_SANITIZE_FULL_SPECIAL_CHARS | FILTER_SANITIZE_ENCODED, ["options" => ["default" => "index"]]));
} catch(Exception $oException) {    
    echo $oException->getMessage();
}
?>
