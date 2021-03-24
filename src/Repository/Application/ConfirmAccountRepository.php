<?php declare(strict_types=1);

namespace App\Repository\Application;

use App\Entity\Application\ConfirmAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ConfirmAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConfirmAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConfirmAccount[]    findAll()
 * @method ConfirmAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfirmAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfirmAccount::class);
    }
}
