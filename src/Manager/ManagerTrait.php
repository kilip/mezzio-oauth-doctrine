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
use Doctrine\Persistence\ObjectRepository;

/**
 * Trait ManagerTrait.
 *
 * @template T
 */
trait ManagerTrait
{
    protected ObjectManager $om;

    protected string $class;

    /**
     * @psalm-suppress PropertyTypeCoercion
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function getRepository(): ObjectRepository
    {
        return $this->om->getRepository($this->class);
    }

    /**
     * @psalm-suppress MixedInferredReturnType
     */
    public function find(string $identifier): ?object
    {
        $repository = $this->getRepository();

        return $repository->find($identifier);
    }

    protected function doSave(object $object): void
    {
        $this->om->persist($object);
        $this->om->flush();
    }

    protected function doRemove(object $object): void
    {
        $this->om->remove($object);
        $this->om->flush();
    }
}
