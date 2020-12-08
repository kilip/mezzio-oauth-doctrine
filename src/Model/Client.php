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

class Client
{
    private string $identifier;

    private ?string $secret;

    /** @var iterable|RedirectUri[] */
    private iterable $redirectUris = [];

    /** @var iterable|Grant[] */
    private iterable $grants = [];

    /** @var iterable|Scope[] */
    private iterable $scopes = [];

    private bool $active = true;

    private bool $allowPlainTextPkce = false;

    public function __construct(string $identifier, ?string $secret)
    {
        $this->identifier = $identifier;
        $this->secret     = $secret;
    }

    public function __toString(): string
    {
        return $this->getIdentifier();
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    /**
     * @return iterable|RedirectUri[]
     */
    public function getRedirectUris(): iterable
    {
        return $this->redirectUris;
    }

    public function setRedirectUris(RedirectUri ...$redirectUris): self
    {
        $this->redirectUris = $redirectUris;

        return $this;
    }

    /**
     * @return iterable|Grant[]
     */
    public function getGrants(): iterable
    {
        return $this->grants;
    }

    public function setGrants(Grant ...$grants): self
    {
        $this->grants = $grants;

        return $this;
    }

    /**
     * @return iterable|Scope[]
     */
    public function getScopes(): iterable
    {
        return $this->scopes;
    }

    public function setScopes(Scope ...$scopes): self
    {
        $this->scopes = $scopes;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function isConfidential(): bool
    {
        return ! empty($this->secret);
    }

    public function isPlainTextPkceAllowed(): bool
    {
        return $this->allowPlainTextPkce;
    }

    public function setAllowPlainTextPkce(bool $allowPlainTextPkce): self
    {
        $this->allowPlainTextPkce = $allowPlainTextPkce;

        return $this;
    }
}
