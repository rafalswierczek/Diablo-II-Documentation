<?php declare(strict_types=1);

namespace App\Repository\Application;

use App\Entity\Application\ThreadMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ThreadMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ThreadMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ThreadMessage[]    findAll()
 * @method ThreadMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThreadMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ThreadMessage::class);
    }
}
