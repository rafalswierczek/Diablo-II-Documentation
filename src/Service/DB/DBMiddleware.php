<?php declare(strict_types=1);

namespace App\Service\DB;

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
            throw EntityException::missingEntity($objectId);

        return $entity;
    }

    public function getEntityManager(string $connectionName): \Doctrine\ORM\EntityManagerInterface
    {
        return $this->doctrine->getManager($connectionName);
    }

    public function getConnection(string $connectionName): \Doctrine\DBAL\Connection
    {
        return $this->doctrine->getManager($connectionName)->getConnection();
    }
}