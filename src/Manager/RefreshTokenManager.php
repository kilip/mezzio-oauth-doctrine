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

namespace MezzioOAuthDoctrine\Manager;

use Doctrine\Persistence\ObjectManager;
use MezzioOAuthDoctrine\Contracts\RefreshTokenManagerInterface;
use MezzioOAuthDoctrine\Model\RefreshToken;
use MezzioOAuthDoctrine\Model\RefreshTokenInterface;

class RefreshTokenManager implements RefreshTokenManagerInterface
{
    use ManagerTrait;

    public function __construct(
        ObjectManager $om,
        string $class = RefreshToken::class
    ) {
        $this->om    = $om;
        $this->class = $class;
    }

    public function save(RefreshTokenInterface $refreshToken): void
    {
        $this->doSave($refreshToken);
    }
}
