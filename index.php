<?php
ob_start();
define('RUNTIMESTART',microtime(true));

define('SYSTEM','system');

require_once(SYSTEM.'/systemconfig.php');
require_once(SYSTEM.'/core.php');
require_once(SYSTEM.'/mimes.php');
#require_once(SYSTEM.'/traits.php');
require_once(SYSTEM.'/class/globalsclass.php');
require_once(SYSTEM.'/class/serverclass.php');
require_once(SYSTEM.'/class/fileclass.php');
require_once(SYSTEM.'/class/loadclass.php');
require_once(SYSTEM.'/class/viewclass.php');
require_once(SYSTEM.'/class/loaddbclass.php');
require_once(SYSTEM.'/class/dbclass.php');
require_once(SYSTEM.'/class/sessionclass.php');
require_once(SYSTEM.'/class/cookieclass.php');
require_once(SYSTEM.'/class/inputclass.php');
require_once(SYSTEM.'/class/validateclass.php');
require_once(SYSTEM.'/class/configclass.php');
require_once(SYSTEM.'/class/controller.php');
require_once(SYSTEM.'/class/model.php');
require_once(SYSTEM.'/default.php');

/**
*	@author : Ahsan Zahid Chowdhury
*	@owner : Exception Solutions
*	@email : azc.pavel@gmail.com
*	@phone : +880 1677 533818
*	@since : 2014-04-20
*	@version : 1.0.1
*	Main Index
*/

?>