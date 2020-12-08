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
use MezzioOAuthDoctrine\Filter\ClientFilter;
use MezzioOAuthDoctrine\Model\ClientInterface;

interface ClientManagerInterface
{
    public function save(ClientInterface $client): void;

    public function remove(ClientInterface $client): void;

    public function list(?ClientFilter $clientFilter): iterable;

    /**
     * @return ObjectRepository|DocumentRepository
     */
    public function getRepository(): ObjectRepository;

    /**
     * @return object|ClientInterface|null
     */
    public function find(string $identifier): ?object;
}
