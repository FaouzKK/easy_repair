<?php

require "../vendor/autoload.php";

$route = new AltoRouter() ;

$route->map("GET","/", function() {

    //backend

    require "../views/home.php" ;

},"home") ;

$route->map("GET","/about/[i:id]", function($id) {

    dd($id) ;

}) ;


$match = $route->match() ;

if (is_array($match) && is_callable($match['target'])) {

    call_user_func_array($match['target'], $match['params']) ;

} else {

    // no route was matched
    echo "no route was matched";
}