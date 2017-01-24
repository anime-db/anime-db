<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\DependencyInjection;

use AnimeDb\Bundle\AnimeDbBundle\DependencyInjection\AnimeDbAnimeDbExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AnimeDbAnimeDbExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        /* @var $container ContainerBuilder */
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $di = new AnimeDbAnimeDbExtension();
        $di->load([], $container);
    }
}
