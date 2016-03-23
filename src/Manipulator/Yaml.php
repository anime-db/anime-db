<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Manipulator;

use Symfony\Component\Yaml\Yaml as Util;

/**
 * Yaml manipulator
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Manipulator
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Yaml extends FileContent
{
    /**
     * @return array
     */
    protected function getContent()
    {
        return Util::parse(parent::getContent());
    }

    /**
     * @param string $content
     */
    protected function setContent($content)
    {
        parent::setContent(Util::dump($content));
    }
}
