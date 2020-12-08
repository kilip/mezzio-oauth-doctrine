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
use MezzioOAuthDoctrine\Contracts\AccessTokenManagerInterface;
use MezzioOAuthDoctrine\Model\AccessToken;
use MezzioOAuthDoctrine\Model\AccessTokenInterface;

final class AccessTokenManager implements AccessTokenManagerInterface
{
    use ManagerTrait;

    public function __construct(ObjectManager $om, string $class = AccessToken::class)
    {
        $this->om    = $om;
        $this->class = $class;
    }

    public function save(AccessTokenInterface $accessToken): void
    {
        $this->doSave($accessToken);
    }
}
