<?php declare(strict_types = 1);

namespace App\Exception;

class DuplicationException extends TableException
{
    public static function duplicateHeaderNames(string $details)
    {
        return new self("Found duplicated values in first row (header) of table. $details");
    }
}