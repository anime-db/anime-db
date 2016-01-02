<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\AddConfig;

/**
 * Job: Add package to routing
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Add extends AddConfig
{
    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\AnimeDbBundle\Composer\Job\AddConfig::execute()
     */
    public function execute()
    {
        // This package has a file routing.xml, which contains the list of services,
        // rather than being contain the list of routers
        if ($this->getPackage()->getName() != 'sensio/framework-extra-bundle') {
            $this->addConfig('routing', 'anime-db-routing');
        }
    }

    /**
     * Do add config
     *
     * @param string $bundle
     * @param string $extension
     * @param string $path
     */
    protected function doAddConfig($bundle, $extension, $path)
    {
        /* @var $manipulator \AnimeDb\Bundle\AnimeDbBundle\Manipulator\Routing */
        $manipulator = $this->getContainer()->getManipulator('routing');
        $manipulator->addResource($this->getRoutingNodeName(), $bundle, $extension, $path);
    }
}
