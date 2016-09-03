<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Config;

/**
 * Job: Remove package from config.
 */
class Remove extends Job
{
    /**
     * @var int
     */
    const PRIORITY = self::PRIORITY_INSTALL;

    /**
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
            /* @var $bundle Bundle */
            $bundle = new $bundle();
            /* @var $manipulator Config */
            $manipulator = $this->getContainer()->getManipulator('config');
            $manipulator->removeResource($bundle->getName());
        }
    }
}
