<?php declare(strict_types=1);

namespace App\Repository\Application;

use App\Entity\Application\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getUserByRole($code)
    {
        return $this->createQueryBuilder('u')
            ->select('u', 'r')
            ->leftJoin('u.Roles', 'r')
            ->where('r.Code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getSingleResult();
    }

    public function loadUserByUsername($login)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :query OR u.email = :query')
            ->setParameter('query', $login)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
