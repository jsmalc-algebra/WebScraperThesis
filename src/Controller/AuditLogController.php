<?php

namespace App\Controller;

use App\Enum\AuditEvent;
use App\Repository\AuditLogRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class AuditLogController extends AbstractController
{
    #[Route('/logs', name: 'app_log', methods: ['GET'])]
    public function index(AuditLogRepository $auditLogRepository, UserRepository $userRepository, Request $request): Response
    {

        if (!$request->query->has('filter')) {
            $logs = $auditLogRepository->findAll();
        } else {
            dump("request: ",$request->query->all());
            $event=$user=$from=$to=null;

           if ($request->query->getString("event") !== '') {
               $event = AuditEvent::tryFrom($request->query->getString("event"));
           }

           if ($request->query->getString("user") > 0) {
               $user = $userRepository->findById($request->query->getInt("user"));
               //dump("User: ",$user);
           }

           if ($request->query->getString("from") !== '') {
               $from = \DateTimeImmutable::createFromFormat('!Y-m-d', $request->query->getString("from"));
           }

           if ($request->query->getString("to") !== '') {
               $to = \DateTimeImmutable::createFromFormat('!Y-m-d', $request->query->getString("to"));
               $to = $to ? $to->setTime(23, 59, 59) : null;
           }

           $logs = $auditLogRepository->findFiltered($event,$user, $from, $to);
           dump("Logs: ",$logs); //Debug
        }

        $users = $userRepository->findAll();
        $events = array_column(AuditEvent::cases(), 'value');

        return $this->render('log/index.html.twig', [
            'logs' => $logs,
            'users' => $users,
            'events' => $events,
        ]);
    }
}
