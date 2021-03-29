<?php declare(strict_types = 1);

namespace App\Exception;

use Doctrine\ORM\Query\QueryException;

class InvalidQueryException extends QueryException
{
    public static function unexpectedQueryResult(string $details = '')
    {
        return new self('Unexpected query result. '.$details);
    }

    public static function unexpectedTransactionResult(string $details = '')
    {
        return new self('Unexpected transaction result. '.$details);
    }
}