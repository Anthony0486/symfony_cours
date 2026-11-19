<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    //    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    //    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findPublishedAfter(\DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('b')
            ->select(
                'b.title, b.author, b.publishAt'
            )
            ->andWhere('b.publishAt >= :publishAt')
            ->setParameter('publishAt', $date)
            ->orderBy('b.publishAt', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    public function findByCategory(string $categoryName): array
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('b.categories', 'c')
            ->andWhere('c.name = :name')
            ->setParameter('name', $categoryName)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
}
