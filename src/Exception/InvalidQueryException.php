<?php declare(strict_types = 1);

namespace App\Exception;

class InvalidQueryException extends \Doctrine\ORM\Query\QueryException
{
    public static function unexpectedQueryResult(string $message)
    {
        return new self('Unexpected query result: '.$message);
    }
}