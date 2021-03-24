<?php declare(strict_types = 1);

namespace App\Exception;

class ErrorException extends \Error
{
    public static function errorFound(string $details)
    {
        return new self("Found an error. $details");
    }
}