<?php

declare(strict_types=1);

namespace MezzioOAuthDoctrine\Model;

use DateTimeInterface;

interface RefreshTokenInterface
{
    public function getIdentifier(): string;

    public function getExpiry(): DateTimeInterface;

    public function getAccessToken(): ?AccessToken;

    public function isRevoked(): bool;

    public function revoke(): self;
}