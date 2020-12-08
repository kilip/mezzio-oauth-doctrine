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

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use MezzioOAuthDoctrine\Model\AuthorizationCodeInterface;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Trait TestManagerTrait.
 */
trait TestManagerTrait
{
    use ProphecyTrait;

    /** @var ObjectProphecy|ObjectManager */
    protected $om;

    /** @var ObjectProphecy|ObjectRepository */
    protected $repository;

    public function setUp(): void
    {
        $this->om         = $this->prophesize(ObjectManager::class);
        $this->repository = $this->prophesize(ObjectRepository::class);
    }

    public function createTarget(): object
    {
        $this->om->getRepository($this->entityClass)
            ->willReturn($this->repository->reveal());

        return new $this->managerClass($this->om->reveal(), $this->entityClass);
    }

    /**
     * @psalm-suppress MixedMethodCall
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress ArgumentTypeCoercion
     * @psalm-suppress MissingReturnType
     * @psalm-suppress MixedArgument
     * @psalm-suppress PossiblyUndefinedMethod
     */
    public function testGetRepository()
    {
        $target = $this->createTarget();

        $this->assertSame($this->repository->reveal(), $target->getRepository());
    }

    /**
     * @psalm-suppress MixedMethodCall
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress ArgumentTypeCoercion
     * @psalm-suppress MissingReturnType
     */
    public function testFind()
    {
        $expected = $this->prophesize(AuthorizationCodeInterface::class)->reveal();

        $this->repository->find('id')
            ->shouldBeCalledOnce()
            ->willReturn($expected);

        $result = $this->createTarget()->find('id');
        $this->assertSame($expected, $result);
    }

    /**
     * @psalm-suppress MixedMethodCall
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress ArgumentTypeCoercion
     * @psalm-suppress MissingReturnType
     */
    public function testSave()
    {
        $token = $this->prophesize($this->entityClass)->reveal();
        $this->om->persist($token)
            ->shouldBeCalledOnce();
        $this->om->flush()
            ->shouldBeCalledOnce();

        $this->createTarget()->save($token);
    }
}
