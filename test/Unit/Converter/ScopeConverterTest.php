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

namespace MezzioOAuthDoctrine\Tests\Unit\Converter;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use MezzioOAuthDoctrine\Converter\ScopeConverter;
use MezzioOAuthDoctrine\Model\Scope as ScopeModel;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ScopeConverterTest extends TestCase
{
    use ProphecyTrait;

    public function testToDomain()
    {
        $entity = $this->prophesize(ScopeEntityInterface::class);
        $entity->getIdentifier()
            ->shouldBeCalled()
            ->willReturn('scope-id');

        $target = new ScopeConverter();
        $result = $target->toDomain($entity->reveal());

        $this->assertInstanceOf(ScopeModel::class, $result);
        $this->assertSame('scope-id', (string) $result);
    }

    public function testToDomainArray()
    {
        $entity = $this->prophesize(ScopeEntityInterface::class);
        $entity->getIdentifier()
            ->shouldBeCalled()
            ->willReturn('scope-id');

        $target = new ScopeConverter();
        $result = $target->toDomainArray([$entity->reveal()]);

        $this->assertIsArray($result);
        $this->assertInstanceOf(ScopeModel::class, $result[0]);
    }

    public function testToLeague()
    {
        $model = $this->prophesize(ScopeModel::class);
        $model->__toString()->willReturn('scope')->shouldBeCalled();

        $target = new ScopeConverter();
        $result = $target->toLeague($model->reveal());

        $this->assertInstanceOf(ScopeEntityInterface::class, $result);
    }

    public function testToLeagueArray()
    {
        $model = $this->prophesize(ScopeModel::class);
        $model->__toString()->willReturn('scope')->shouldBeCalled();

        $target = new ScopeConverter();
        $result = $target->toLeagueArray([$model->reveal()]);

        $this->assertIsArray($result);
        $this->assertInstanceOf(ScopeEntityInterface::class, $result[0]);
    }
}
