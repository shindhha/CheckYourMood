
<meta HTTP-EQUIV="Pragma" content="no-cache">
<meta HTTP-EQUIV="Expires" content="-1">
<?php
ini_set('display_errors', 'on');

spl_autoload_extensions(".php");
spl_autoload_register();

use yasmf\DataSource;
use yasmf\Router;

$dataSource = new DataSource(
    $host = 'localhost',
    $port = '3306', # to change with the port your mySql server listen to
    $db = 'check_your_mood', # to change with your db name
    $user = 'root', # to change with your db user name
    $pass = 'root', # to change with your db password
    $charset = 'utf8mb4'
);

$router = new Router() ;
$router->route($dataSource);


