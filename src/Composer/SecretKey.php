<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer;

class SecretKey
{
    const KEY_LENGTH = 32;

    /**
     * @return string
     */
    public static function generate()
    {
        $secret = '';
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for ($i = 0; $i < self::KEY_LENGTH; ++$i) {
            $secret .= $chars[rand(0, strlen($chars) - 1)];
        }

        return $secret;
    }
}
