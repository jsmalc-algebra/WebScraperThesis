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
    public const string TIER_CHANGE = 'USER_TIER_CHANGE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE, self::TIER_CHANGE], true)
            && $subject instanceof User;
    } // Checks if the voter is a User and if the attribute is relevant

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $currentUser = $token->getUser(); // It gets whoever is logged in


        if (!$currentUser instanceof User) {
            return false;
        } // Makes sure that whoever it is, is a proper user (and not for instance an anonymous one)

        if (in_array('ROLE_ADMIN', $currentUser->getRoles(), true) and $attribute !== self::TIER_CHANGE) {
            return true;
        } // Allow if the user is an admin, but don't allow them onto the tier change portal

        return $currentUser === $subject; // Or if the logged in and viewed user are one and the same
    }
}
