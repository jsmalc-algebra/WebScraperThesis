<?php

// src/Security/UserVoter.php
namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

class UserVoter extends Voter
{
    public const string VIEW = 'USER_VIEW';
    public const string EDIT = 'USER_EDIT';
    public const string DELETE = 'USER_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT], true)
            && $subject instanceof User;
    } // Checks if the voter is a User and if the attribute is relevant

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $currentUser = $token->getUser(); // It gets whoever is logged in


        if (!$currentUser instanceof User) {
            return false;
        } // Makes sure that whoever it is, is a proper user (and not for instance an anonymous one)

        if (in_array('ROLE_ADMIN', $currentUser->getRoles(), true)) {
            return true;
        } // Allow if the user is an admin

        return $currentUser === $subject; // Or if the logged in and viewed user are one and the same
    }
}
