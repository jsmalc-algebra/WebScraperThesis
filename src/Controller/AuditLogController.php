<?php

namespace App\Controller;

use App\Entity\AuditLog;
use App\Entity\User;
use App\Enum\AuditEvent;
use App\Repository\AuditLogRepository;
use App\Repository\UserRepository;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuditLogController extends AbstractController
{
    #[Route('/logs', name: 'app_log', methods: ['POST','GET'])]
    public function index(AuditLogRepository $auditLogRepository, UserRepository $userRepository, Request $request): Response
    {
        if ($request->isMethod(Request::METHOD_GET)) {
            $logs = $auditLogRepository->findAll();
            $users = $userRepository->findAll();
            $events = array_column(AuditEvent::cases(), 'name');


        } else {throw new \LogicException(`Yet to be implemented`);}

        return $this->render('log/index.html.twig', [
            'logs' => $logs,
            'users' => $users,
            'events' => $events,
        ]);
    }

    public function filterByEvent(array $logs,AuditEvent $auditEvent) : array
    {
        return array_filter(
            $logs,
            fn (AuditLog $log) => $log->getEvent() === $auditEvent
        );
    }

    public function filterByUser(array $logs, User $user) : array
    {
        return array_filter(
            $logs,
            fn (AuditLog $log) => $log->getUser() === $user
        );
    }

    public function filterBetweenDates(array $logs, DateTimeInterface $from, DateTimeInterface $to) : array
    {
        $start = \DateTimeImmutable::createFromInterface($from)->setTime(0, 0);
        $end = \DateTimeImmutable::createFromInterface($to)->setTime(0, 0)->modify('+1 day');

        return array_filter(
            $logs,
            fn(AuditLog $log) => $log->getTimetag() >= $start and $log->getTimetag() <= $end
        );
    }
}
