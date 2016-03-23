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

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;

/**
 * Job: Remove package from config
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Remove extends Job
{
    /**
     * Job priority
     *
     * @var integer
     */
    const PRIORITY = self::PRIORITY_INSTALL;

    /**
     * Package bundle name
     *
     * @var string|null
     */
    protected $bundle;

    public function register()
    {
        // get the bundle name before remove package, because then it would impossible to do
        $this->bundle = $this->getPackageBundle();
    }

    public function execute()
    {
        if ($this->bundle !== null) {
            $bundle = $this->bundle;
            /* @var $bundle \Symfony\Component\HttpKernel\Bundle\Bundle */
            $bundle = new $bundle();
            /* @var $manipulator \AnimeDb\Bundle\AnimeDbBundle\Manipulator\Config */
            $manipulator = $this->getContainer()->getManipulator('config');
            $manipulator->removeResource($bundle->getName());
        }
    }
}
