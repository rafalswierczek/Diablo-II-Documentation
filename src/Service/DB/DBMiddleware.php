<?php declare(strict_types=1);

namespace App\Service\DB;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\EntityException;
use App\Entity\EntityInterface;
use App\Service\Utils\Container;

class DBMiddleware
{
    private $doctrine;
    
    public function __construct(Container $container)
    {
        $this->doctrine = $container->getContainer()->get('doctrine');
    }

    protected function getEntity(string $action, string $entityName, string $connectionName, $objectId = null): EntityInterface
    {
        if($action === 'add')
            $entity = new $entityName();
        else
            $entity = $this->getEntityManager($connectionName)->getRepository($entityName)->find($objectId);

        if(!$entity)
            throw EntityException::cannotFindEntity($entityName, $objectId);

        return $entity;
    }

    public function getEntityManager(string $connectionName): EntityManagerInterface
    {
        return $this->doctrine->getManager($connectionName);
    }

    public function getConnection(string $connectionName): Connection
    {
        return $this->doctrine->getManager($connectionName)->getConnection();
    }
}