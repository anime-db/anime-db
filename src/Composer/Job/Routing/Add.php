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

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\BaseAddConfig;
use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Routing;

/**
 * Job: Add package to routing
 */
class Add extends BaseAddConfig
{
    public function execute()
    {
        // This package has a file routing.xml, which contains the list of services,
        // rather than being contain the list of routers
        if ($this->getPackage()->getName() != 'sensio/framework-extra-bundle') {
            $this->addConfig('routing', 'anime-db-routing');
        }
    }

    /**
     * @param string $bundle
     * @param string $extension
     * @param string $path
     */
    protected function doAddConfig($bundle, $extension, $path)
    {
        /* @var $manipulator Routing */
        $manipulator = $this->getContainer()->getManipulator('routing');
        $manipulator->addResource($this->getRoutingNodeName(), $bundle, $extension, $path);
    }
}
