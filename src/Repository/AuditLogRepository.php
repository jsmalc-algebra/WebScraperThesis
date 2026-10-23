<?php

namespace App\Repository;

use App\Entity\AuditLog;
use App\Entity\User;
use App\Enum\AuditEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuditLog>
 */
class AuditLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuditLog::class);
    }

    public function findByEvent(AuditEvent $auditEvent): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.event = :event')
            ->setParameter('event', $auditEvent->value)
            ->getQuery()
            ->getResult();
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findBetweenDates(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        $from = \DateTimeImmutable::createFromInterface($start)->setTime(0, 0);
        $to = \DateTimeImmutable::createFromInterface($end)->setTime(0, 0)->modify('+1 day');

        return $this->createQueryBuilder('a')
            ->andWhere('a.timetag >= :from')
            ->andWhere('a.timetag <= :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('a.timetag', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findFiltered(?AuditEvent $auditEvent, ?User $user, ?\DateTimeImmutable $start, ?\DateTimeImmutable $end): array
    {
        $qb = $this->createQueryBuilder('a')->orderBy('a.timetag', 'DESC');

        if ($auditEvent !== null) {$qb ->andWhere('a.event = :event')->setParameter('event', $auditEvent->value);}
        if($user !== null) {$qb ->andWhere('a.user = :user')->setParameter('user', $user);}
        if ($start !== null) {$qb ->andWhere('a.timetag >= :start')->setParameter('start', $start);}
        if ($end !== null) {$qb ->andWhere('a.timetag <= :end')->setParameter('end', $end);}

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return AuditLog[] Returns an array of AuditLog objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AuditLog
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
