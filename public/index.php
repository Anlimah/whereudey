<?php
require "../bootstrap.php";

use Src\Controller\VideoController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// all of our endpoints start with /whereUdey
// everything else results in a 404 Not Found
if ($uri[1] !== 'whereUdey') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

//
$where = null;
if (isset($uri[2])) {
    $where = $uri[2];
} else {
    header("HTTP/1.1 404 Not Found");
    exit();
}

$when = null;
if (isset($uri[3])) {
    $when = (int) $uri[3];
}

$reqMethod = $_SERVER["REQUEST_METHOD"];


// pass the request method and user ID to the PersonController and process the HTTP request:
$controller = new VideoController($reqMethod, $where, $when);
$controller->processRequest();
