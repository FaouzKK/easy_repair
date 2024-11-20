<?php

namespace App\Class ;

// require '../../vendor/autoload.php';

$envdir = dirname(__DIR__, 2) ;

if (file_exists($envdir))  {
    
    $dotenv = \Dotenv\Dotenv::createImmutable($envdir);
    $dotenv->load();

}
else {
    die("No .env file found");
}

final class Config {

    static function getVar($name) {
        return $_ENV[$name];
    }
}
