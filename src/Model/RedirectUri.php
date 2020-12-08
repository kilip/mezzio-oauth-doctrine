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

use RuntimeException;

use function filter_var;
use function sprintf;

use const FILTER_VALIDATE_URL;

class RedirectUri
{
    private string $redirectUri;

    public function __construct(string $redirectUri)
    {
        if (! filter_var($redirectUri, FILTER_VALIDATE_URL)) {
            throw new RuntimeException(sprintf('The \'%s\' string is not a valid URI.', $redirectUri));
        }

        $this->redirectUri = $redirectUri;
    }

    public function __toString(): string
    {
        return $this->redirectUri;
    }
}
