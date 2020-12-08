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

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Doctrine\Persistence\ObjectRepository;
use MezzioOAuthDoctrine\Model\RefreshTokenInterface;

interface RefreshTokenManagerInterface
{
    /**
     * @return ObjectRepository|DocumentRepository
     */
    public function getRepository(): ObjectRepository;

    /**
     * @return object|RefreshTokenInterface|null
     */
    public function find(string $identifier): ?object;

    public function save(RefreshTokenInterface $refreshToken): void;
}
