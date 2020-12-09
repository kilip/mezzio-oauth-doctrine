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

use League\OAuth2\Server\Entities\UserEntityInterface as LeagueUserEntity;

interface UserEntityInterface extends LeagueUserEntity
{
    /**
     * @param string $identifier
     * @return void
     */
    public function setIdentifier($identifier);
}
