<?php declare(strict_types=1);

namespace App\Service\DB\Documentation;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Table\TableInterface;
use App\Service\DB\DBMiddleware;

abstract class TableDBIterator implements \Iterator
{
    private EntityManagerInterface $entityManager;
    private array $table = [];

    public function __construct(DBMiddleware $DBMiddleware)
    {
        $this->entityManager = $DBMiddleware->getEntityManager('documentation');
    }

    public function setTable(array $table)
    {
        $this->table = $table;
    }

    public function rewind() 
    {
        reset($this->table);
    }

    public function current(): TableInterface
    {
        $entity = current($this->table);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function key()
    {
        return key($this->table);
    }

    public function next()
    {
        next($this->table);
    }

    public function valid(): bool
    {
        $current = current($this->table);
        return !empty($current) && $current instanceof TableInterface;
    }
}