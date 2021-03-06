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
use MezzioOAuthDoctrine\Contracts\AuthorizationCodeManagerInterface;
use MezzioOAuthDoctrine\Model\AuthorizationCodeInterface;

class AuthorizationCodeManager implements AuthorizationCodeManagerInterface
{
    use ManagerTrait;

    public function __construct(ObjectManager $om, string $class)
    {
        $this->om    = $om;
        $this->class = $class;
    }

    public function save(AuthorizationCodeInterface $authorizationCode): void
    {
        $this->doSave($authorizationCode);
    }
}
