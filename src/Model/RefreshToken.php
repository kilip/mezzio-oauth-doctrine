<?php

/*
 * This file is part of the MezzioOAuthDoctrine project.
 *
 * (c) Anthonius Munthi <https://itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MezzioOAuthDoctrine\Model;

use DateTimeInterface;

class RefreshToken implements RefreshTokenInterface
{
    private string $identifier;

    private DateTimeInterface $expiry;

    private ?AccessToken $accessToken;

    private bool $revoked = false;

    public function __construct(string $identifier, DateTimeInterface $expiry, ?AccessToken $accessToken = null)
    {
        $this->identifier  = $identifier;
        $this->expiry      = $expiry;
        $this->accessToken = $accessToken;
    }

    public function __toString(): string
    {
        return $this->getIdentifier();
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getExpiry(): DateTimeInterface
    {
        return $this->expiry;
    }

    public function getAccessToken(): ?AccessToken
    {
        return $this->accessToken;
    }

    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    public function revoke(): self
    {
        $this->revoked = true;

        return $this;
    }
}
