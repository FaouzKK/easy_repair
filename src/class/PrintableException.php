<?php

namespace App\Class;

use Exception;

/**
 * Cette Classe initie des erreur que nous pouvons afficher sur la page Web
 */
class PrintableException extends Exception
{
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
