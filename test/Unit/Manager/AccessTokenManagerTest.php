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

namespace MezzioOAuthDoctrine\Tests\Unit\Manager;

use MezzioOAuthDoctrine\Manager\AccessTokenManager;
use MezzioOAuthDoctrine\Model\AccessToken;
use PHPUnit\Framework\TestCase;

class AccessTokenManagerTest extends TestCase
{
    use TestManagerTrait;

    protected string $managerClass = AccessTokenManager::class;

    protected string $entityClass = AccessToken::class;
}
