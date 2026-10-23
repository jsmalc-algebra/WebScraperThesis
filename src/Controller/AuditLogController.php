<?php

namespace App\Controller;

use App\Enum\AuditEvent;
use App\Repository\AuditLogRepository;
use App\Repository\UserRepository;
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
        } else {
            $event=$user=$from=$from=$to=null;

           if ($request->request->getString("event") !== '') {
               $event = AuditEvent::tryFrom($request->request->getString("event"));
           }

           if ($request->request->getString("user") !== '') {
               $user = $userRepository->find($request->request->getString("user")[0]);
           }

           if ($request->request->getString("from") !== '') {
               $from = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $request->request->getString("from"));
           }

           if ($request->request->getString("to") !== '') {
               $to = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $request->request->getString("to"));
           }

           $logs = $auditLogRepository->findFiltered($user, $from, $to, $event);
        }

        $users = $userRepository->findAll();
        $events = array_column(AuditEvent::cases(), 'name');

        return $this->render('log/index.html.twig', [
            'logs' => $logs,
            'users' => $users,
            'events' => $events,
        ]);
    }
}
