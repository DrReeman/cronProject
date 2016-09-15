<?php
//ini_set('display_errors', 1);

$directory = __DIR__;

require_once $directory . '/application/controller/Controller.php';
require_once $directory . '/application/view.php';
require_once $directory . '/application/classes/connection.php';
require_once $directory . '/application/classes/Parser.php';
require_once $directory . '/application/model/Environment.php';
require_once $directory . '/application/model/CronConfig.php';
define('VIEWS_BASEDIR', dirname(__FILE__).'/application/view/');

use Controller\Controller;

$action = '';
$param = null;
if ( !empty($_POST['action']) )
    {
        $action = $_POST['action'];
    }
else
    {
        $action = 'index';
    }

if ( !empty($_POST['param']) )
    {
        $param = $_POST['param'];
    }

$actionName = $action.'Action';
$controller = new Controller();

if ( method_exists ( $controller, $actionName ) )
    {
        $controller->$actionName ( $param );
    }
else
    {
        errorPage404();
    }

function errorPage404()
{
    $host = 'http://'.$_SERVER['HTTP_HOST'].'/';
    header('HTTP/1.1 404 Not Found');
    header("Status: 404 Not Found");
    header('Location:'.$host.'404');
}
