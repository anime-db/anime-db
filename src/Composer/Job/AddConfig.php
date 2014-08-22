<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;

/**
 * Job: Add package config
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class AddConfig extends Job
{
    /**
     * Job priority
     *
     * @var integer
     */
    const PRIORITY = self::PRIORITY_INSTALL;

    /**
     * (non-PHPdoc)
     * @see AnimeDb\Bundle\AnimeDbBundle\Composer\Job.Job::execute()
     */
    public function execute()
    {
        $config = $this->getPackageConfig();
        $bundle = $this->getPackageBundle();
        if ($config && $bundle) {
            $bundle = new $bundle();
            $info = pathinfo($config);
            $path = $info['dirname'] != '.' ? $info['dirname'].'/'.$info['filename'] : $info['filename'];
            $this->addConfig($bundle->getName(), $info['extension'], $path);
        }
    }

    /**
     * Add config
     *
     * @param string $bundle
     * @param string $extension
     * @param string $path
     */
    abstract protected function addConfig($bundle, $extension, $path);

    /**
     * Get package config
     *
     * @return string
     */
    abstract protected function getPackageConfig();
}