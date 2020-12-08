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

namespace MezzioOAuthDoctrine\Model;

class Scope
{
    private string $scope;

    public function __construct(string $scope)
    {
        $this->scope = $scope;
    }

    public function __toString(): string
    {
        return $this->scope;
    }
}
