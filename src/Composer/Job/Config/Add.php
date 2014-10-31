<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\AddConfig;

/**
 * Job: Add package to config
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config
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
        $this->addConfig('config', 'anime-db-config');
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
        $this->getContainer()->getManipulator('config')->addResource($bundle, $extension, $path);
    }
}
