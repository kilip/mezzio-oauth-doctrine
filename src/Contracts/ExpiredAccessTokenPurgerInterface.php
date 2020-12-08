<?php

declare(strict_types=1);

namespace MezzioOAuthDoctrine\Contracts;


interface ExpiredAccessTokenPurgerInterface
{
    public function clearExpiredAccessToken();
}