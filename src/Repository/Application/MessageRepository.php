<?php declare(strict_types=1);

namespace App\Repository\Application;

use App\Entity\Application\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function getMessagesByThread(int $threadID)
    {
        return $this->createQueryBuilder('m')
            ->select('m')
            ->innerJoin('m.Thread', 't')
            ->innerJoin('m.User', 'u')
            ->where("t.ID = :threadID")
            ->orderBy('m.AddDate', 'DESC')
            ->setParameter(':threadID', $threadID)
            ->getQuery()
            ->getResult();
    }
}