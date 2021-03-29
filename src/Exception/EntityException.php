<?php declare(strict_types = 1);

namespace App\Exception;

use Doctrine\ORM\ORMException;
use App\Entity\EntityInterface;

class EntityException extends ORMException
{
    public static function cannotFindEntity(string $entityFQN, $primaryKeyValue)
    {
        return new self("Cannot find entity $entityFQN for primary key: $primaryKeyValue");
    }
}