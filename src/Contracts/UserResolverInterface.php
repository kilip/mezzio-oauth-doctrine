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

namespace MezzioOAuthDoctrine\Contracts;

use MezzioOAuthDoctrine\Model\ClientInterface;
use MezzioOAuthDoctrine\Model\Grant;

interface UserResolverInterface
{
    public function resolveUser(
        string $username,
        string $password,
        Grant $grant,
        ClientInterface $client
    ): ?OAuthUserInterface;
}
