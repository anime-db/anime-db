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

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config\Config as BaseConfig;

/**
 * Job: Add package to config
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Add extends BaseConfig
{
    /**
     * (non-PHPdoc)
     * @see AnimeDb\Bundle\AnimeDbBundle\Composer\Job.Job::execute()
     */
    public function execute()
    {
        $config = $this->getPackageConfig();
        $bundle = $this->getContainer()->getPackageBundle($this->getPackage());
        if ($config && $bundle) {
            $bundle = new $bundle();
            $info = pathinfo($config);
            $path = $info['dirname'] != '.' ? $info['dirname'].'/'.$info['filename'] : $info['filename'];
            $this->manipulator->addResource($bundle->getName(), $info['extension'], $path);
        }
    }
}