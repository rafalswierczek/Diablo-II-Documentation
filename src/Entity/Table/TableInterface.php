<?php declare(strict_types = 1);

namespace App\Entity\Table;

interface TableInterface
{
    public function getFileName(): string;
}