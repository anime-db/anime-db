<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\BaseAddConfig;
use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Config;

/**
 * Job: Add package to config.
 */
class Add extends BaseAddConfig
{
    public function execute()
    {
        $this->addConfig('config', 'anime-db-config');
    }

    /**
     * @param string $bundle
     * @param string $extension
     * @param string $path
     */
    protected function doAddConfig($bundle, $extension, $path)
    {
        /* @var $manipulator Config */
        $manipulator = $this->getContainer()->getManipulator('config');
        $manipulator->addResource($bundle, $extension, $path);
    }
}
