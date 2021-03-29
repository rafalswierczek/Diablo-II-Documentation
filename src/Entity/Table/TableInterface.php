<?php declare(strict_types = 1);

namespace App\Entity\Table;

use App\Entity\EntityInterface;

interface TableInterface extends EntityInterface
{
    public function getFileName(): string;
}