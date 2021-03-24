<?php declare(strict_types=1);

namespace App\Service\Documentation\Table\Tool;

use App\Entity\Table\TableInterface;

class TableIterator implements \Iterator
{
    public array $table;

    public function rewind(): ?TableInterface
    {
        return reset(self::$table) ?: null;
    }

    public function key(): ?int
    {
        return key(self::$table);
    }
  
    public function current(): ?TableInterface
    {
        return current(self::$table) ?: null;
    }
  
    public function next(): ?TableInterface
    {
        return next(self::$table) ?: null;
    }
  
    public function valid(): bool
    {
        return (!empty(self::$table[$this->key()]) && self::$table[$this->key()] instanceof TableInterface);
    }
}