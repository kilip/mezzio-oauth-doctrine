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

use MezzioOAuthDoctrine\Contracts\OAuthUserInterface;
use MezzioOAuthDoctrine\Contracts\UserEntityInterface;
use MezzioOAuthDoctrine\Converter\UserConverter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class UserConverterTest extends TestCase
{
    use ProphecyTrait;

    public function testToLeague()
    {
        $user = $this->prophesize(OAuthUserInterface::class);
        $user->getOAuthIdentifier()->willReturn('user-id')->shouldBeCalled();

        $target = new UserConverter();
        $result = $target->toLeague($user->reveal());

        $this->assertInstanceOf(UserEntityInterface::class, $result);
        $this->assertSame('user-id', $result->getIdentifier());
    }
}
