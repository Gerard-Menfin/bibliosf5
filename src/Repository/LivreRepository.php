<?php

namespace App\Repository;

use App\Entity\Emprunt;
use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Livre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livre[]    findAll()
 * @method Livre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    /**
     * @return Livre[] Returns an array of Livre objects
     */
    public function recherche($mot)
    {
        return $this->createQueryBuilder('l')
            ->where('l.titre LIKE :mot OR l.auteur LIKE :mot')
            ->setParameter('mot', "%$mot%")
            ->orderBy('l.auteur', 'ASC')
            ->addOrderBy('l.titre')
            ->getQuery()
            ->getResult()
        ;
    }


    public function findLivresIndisponibles()
    {
        /* SELECT l.* 
            FROM livre l JOIN emprunt e ON l.id = e.livre_id 
            WHERE e.date_retour IS NULL */
            return $this->createQueryBuilder("l")
                    ->join(Emprunt::class, "e", "WITH", "l.id = e.livre")
                    ->where("e.date_retour IS NULL")
                    ->orderBy("l.auteur")
                    ->addOrderBy("l.titre")
                    ->getQuery()
                    ->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Livre
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
