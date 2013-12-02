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
 * Job: Remove package from config
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Remove extends BaseConfig
{
    /**
     * (non-PHPdoc)
     * @see AnimeDb\Bundle\AnimeDbBundle\Composer\Job.Job::execute()
     */
    public function execute()
    {
        if ($bundle = $this->getContainer()->getPackageBundle($this->getPackage())) {
            $bundle = new $bundle();
            $this->manipulator->removeResource($bundle->getName());
        }
    }
}