<?php

namespace App\PrometheusMetrics;

use App\Repository\UserRepository;
use Prometheus\CollectorRegistry;


class UserNumberMetrics
{
    private array $userRoles = ["ROLE_FREE",'ROLE_PREMIUM','ROLE_ADMIN'];

    public function __construct(
        private readonly CollectorRegistry $registry,
        private readonly UserRepository    $userRepository,
    ){}

    public function totalUsers(): void
    {
        $this->registry
            ->getOrRegisterGauge('scraper_app','total_users','Number of registered users')
            ->set($this->userRepository->count([]));
    }

    public function totalUsersPerRole():void
    {
        foreach ($this->userRoles as $role) {
            $this->registry
                ->getOrRegisterGauge('scraper_app','total_users_per_role_'.$role, 'Number of registered users with the role '.$role)
                ->set($this->userRepository->countByRole($role));
        }
    }
}
