<?php

/*
 * This file is part of the Aureja package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aureja\Provider\JobQueue\Exception;

use InvalidArgumentException;

/**
 * Class ServiceNotFoundException.
 * 
 * @package Aureja\Provider\JobQueue\Exception
 */
class ServiceNotFoundException extends InvalidArgumentException
{
    /**
     * @param string $id
     *
     * @return InvalidArgumentException
     */
    public static function create($id)
    {
        return new self(sprintf('Not found service "%s".', $id));
    }
}
