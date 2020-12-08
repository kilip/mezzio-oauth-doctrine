<?php

declare(strict_types=1);

namespace MezzioOAuthDoctrine\Purger;


interface ExpiredDataPurgerInterface
{
    public function clearExpiredAccessToken():int;

    public function clearExpiredAuthorizationCode(): int;

    public function clearExpiredRefreshToken(): int;
}