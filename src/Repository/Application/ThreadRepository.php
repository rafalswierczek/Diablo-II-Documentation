<?php declare(strict_types=1);

namespace App\Repository\Application;

use App\Entity\Application\Thread;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Thread|null find($id, $lockMode = null, $lockVersion = null)
 * @method Thread|null findOneBy(array $criteria, array $orderBy = null)
 * @method Thread[]    findAll()
 * @method Thread[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThreadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Thread::class);
    }

    public function getThreadsByUserID(int $userID)
    {
        return $this->createQueryBuilder('t')
            ->select('t')
            ->where("t.User1ID = $userID OR t.User2ID = $userID")
            ->getQuery()
            ->getResult();
    }
    
    public function getUniqueUserPair(int $user1ID, int $user2ID)
    {
        return $this->createQueryBuilder('t')
            ->select('t')
            ->where("(t.User1ID = $user1ID OR t.User1ID = $user2ID) AND (t.User2ID = $user1ID OR t.User2ID = $user2ID)")
            ->getQuery()
            ->getOneOrNullResult();
    }
}
