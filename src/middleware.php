<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

spl_autoload_register(function($name){
    
    $classname = str_replace('\\', '/', SP_DIR . '/src/' . $name . '.php'); 

    if(file_exists($classname))
        return include $classname;

});
