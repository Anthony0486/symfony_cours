<?php

namespace App\Repository;

use App\dto\UserListDto;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneUserListDtoById(int $id): ?UserListDto
//    {
//        return $this->createQueryBuilder('u')
//             ->select(
//                 'NEW App\Dto\UserListDto(
//                 u.firstname, 
//                 u.lastnale, 
//                 u.email)')
//            ->andWhere('u.id = :id')
//            ->setParameter('id', $id)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
//    public function findAllUserListDto(): ?array
//    {
//        return $this->createQueryBuilder('u')
//             ->select(
//                 'NEW App\Dto\UserListDto(
//                 u.firstname, 
//                 u.lastnale, 
//                 u.email)')
//            ->getQuery()
//            ->getResult()
//        ;
//    }
}
