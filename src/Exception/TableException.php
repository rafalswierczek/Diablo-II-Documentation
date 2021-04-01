<?php declare(strict_types = 1);

namespace App\Exception;

class TableException extends \Exception
{
    public static function errorFound(string $details)
    {
        return new self("Found an error while creating documentation. $details");
    }
}