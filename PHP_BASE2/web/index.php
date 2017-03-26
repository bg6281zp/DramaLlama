<?php

// required to serve static files using PHP's built-in web server
// for other web server configuration information, see:
//   http://silex.sensiolabs.org/doc/web_servers.html
$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

// The web directory is exposed to the world through the web server.
// So, just to be safe, put all the PHP application code outside of the web
// directory.  That way if PHP gets turned off and the web server sends
// index.php in plain text, no one will get any sensitive information
// from the PHP application.
//
// returns the services container
//   note that a file that is called with require or include can return a value
$app = require_once __DIR__.'/../app/app.php';

// run the application
$app->run();