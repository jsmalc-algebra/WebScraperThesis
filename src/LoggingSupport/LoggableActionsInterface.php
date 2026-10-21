<?php

namespace App\LoggingSupport;

use App\Entity\User;

interface LoggableActionsInterface
{ // If User is a requirement it is because it cannot be fetched via symphony security in the given moment and most be received "manually"
    public function userRegistration(User $user): void;
    public function userLogin(User $user): void;
    public function userLogout(User $user): void;
    public function userScrape(string $details): void;
    public function userSelfEdit(string $details): void;
    public function userTierChange(string $details): void;
    public function userSelfDelete(string $details): void;
    public function adminUserCreation(string $details): void;
    public function adminUserEdit(string $details): void;
    public function adminUserDelete(string $details): void;

}
