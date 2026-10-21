<?php

namespace App\patterns;

interface LoggableActionsInterface
{
    public function UserRegistration($event,$user);
    public function UserLogin($event,$user);
    public function UserLogout($event,$user);
    public function UserScrape($event,$user,$details);
    public function UserSelfEdit($event,$user,$details);
    public function UserTierChange($event,$user,$details);
    public function UserSelfDelete($event,$user);
    public function AdminUserCreation($event,$user,$details);
    public function AdminUserEdit($event,$user,$details);
    public function AdminUserDelete($event,$user);

}
