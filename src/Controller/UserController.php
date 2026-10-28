<?php

namespace App\Controller;

use App\Entity\User;
use App\LoggingSupport\LoggingActions;
use App\Repository\UserRepository;
use App\Form\UserType;
use App\Security\UserVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;

#[Route('/user')]
final class UserController extends AbstractController
{
    public function __construct(private readonly LoggingActions $logger){}

    #[Route(name: 'app_user_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { #TODO: Add validation that username is unique
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->logger->adminUserCreation("USER CREATED: [".$user->getId()."-".$user->getUsername()."]");

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form
        ]);
    }

    #[Route('/{username}', name: 'app_user_show', methods: ['GET'])]
    #[IsGranted(UserVoter::VIEW, subject: 'user')]
    public function show(
        #[MapEntity(mapping: ['username' => 'username'])]
        User $user
    ): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{username}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    #[IsGranted(UserVoter::EDIT, subject: 'user')]
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['username' => 'username'])]
        User $user,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response
    {
        $oldUsername = $user->getUsername();
        $form = $this->createForm(UserType::class, $user, ['require_password'=>false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            if ($plainPassword) {$user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));}

            $entityManager->flush();

            $this->userEditLogger($form, $user, $oldUsername);

            return $this->redirectToRoute('app_user_show', [
                'username' => $user->getUsername(),
            ],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{username}/delete', name: 'app_user_delete', methods: ['GET', 'POST'])]
    #[IsGranted(UserVoter::DELETE, subject: 'user')]
    public function delete(
        Request $request,
        #[MapEntity(mapping: ['username' => 'username'])]
        User $user,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $details = "USER DELETED: [".$user->getId()."-".$user->getUsername()."]";
            if ($this->isGranted('ROLE_ADMIN')) {$this->logger->adminUserDelete($details);}
            else {$this->logger->userSelfDelete($details);}

            $entityManager->remove($user);
            $entityManager->flush();
        }

        if ($this->isGranted('ROLE_ADMIN')) {return $this->redirectToRoute('app_user_index');}
        else {return $this->redirectToRoute('app_logout');}
    }

    #[Route('/{username}/tier', name: 'app_user_tier', methods: ['GET', 'POST'])]
    #[IsGranted(UserVoter::TIER_CHANGE, subject: 'user')]
    public function tier_change(
        Request $request,
        #[MapEntity(mapping: ['username' => 'username'])]
        User $user,
        EntityManagerInterface $entityManager
    ): Response
    {
        $tier = array_values(array_diff($user->getRoles(), ['ROLE_USER']))[0];

        if ($request->isMethod('POST')) {
            if ($tier === 'ROLE_FREE') {
                $user->setRoles(['ROLE_PREMIUM']);
                $this->logger->userTierChange("USER TIER CHANGED TO PREMIUM");
            }
            else {
                $user->setRoles(['ROLE_FREE']);
                $this->logger->userTierChange("USER TIER CHANGED TO FREE");
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_user_show', [
                'username' => $user->getUsername(),
            ]);
        }

        return $this->render('user/tier_change.html.twig', [
            'tier' => $tier,
            'username' => $user->getUsername(),
        ]);
    }

    public function userEditLogger(FormInterface $form, User $user, string $oldUsername): void
    {
        $details = "";

        if ($user->getUsername() !== $oldUsername) {
            $details = $details."USERNAME CHANGED TO: " . $form->get('username')->getData()."\n";
        }

        if (!empty($form->get('plainPassword')->getData())) {
            $details = $details."PASSWORD CHANGED \n";
        }

        if ($form->has('roles') && !empty($form->get('roles')->getData())) {
            $details = $details."ROLE CHANGED TO: " . $form->get('roles')->getData()[0]."\n";
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $details = $details."CHANGED USER: [".$user->getId()."-".$user->getUsername()."]\n";
            $this->logger->adminUserEdit($details);
        } else {$this->logger->userSelfEdit($details);}
    }
}

