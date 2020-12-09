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

namespace MezzioOAuthDoctrine\Converter;

use MezzioOAuthDoctrine\Contracts\OAuthUserInterface;
use MezzioOAuthDoctrine\Contracts\UserConverterInterface;
use MezzioOAuthDoctrine\Contracts\UserEntityInterface;
use MezzioOAuthDoctrine\League\Entity\UserEntity;

final class UserConverter implements UserConverterInterface
{
    private string $userEntityClass;

    public function __construct(
        string $userEntityClass = UserEntity::class
    ) {
        $this->userEntityClass = $userEntityClass;
    }

    /**
     * @psalm-suppress InvalidStringClass
     */
    public function toLeague(OAuthUserInterface $user): UserEntityInterface
    {
        /** @var UserEntityInterface $userEntity */
        $userEntity = new $this->userEntityClass();
        $userEntity->setIdentifier($user->getOAuthIdentifier());

        return $userEntity;
    }
}
