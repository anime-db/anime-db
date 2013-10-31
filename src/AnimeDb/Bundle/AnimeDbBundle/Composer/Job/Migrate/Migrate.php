<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;
use Composer\Package\Package;

/**
 * Migrate
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Migrate extends Job
{
    /**
     * Job priority
     *
     * @var integer
     */
    const PRIORITY = self::PRIORITY_INIT;

    /**
     * Package
     *
     * @var \Composer\Package\Package
     */
    protected $package;

    /**
     * Construct
     *
     * @param \Composer\Package\Package $package
     */
    public function __construct(Package $package)
    {
        $this->package = $package;
    }

    /**
     * Get path to migrations config file from package
     *
     * @return string|null
     */
    protected function getMigrationsConfig()
    {
        $options = $this->getContainer()->getPackageOptions($this->package);
        // specific location
        if ($options['anime-db-migrations']) {
            return $options['anime-db-migrations'];
        }

        $dir = __DIR__.'/../../../../../../../vendor/'.$this->package->getName().'/';
        if (file_exists($dir.'migrations.yml')) {
            return $dir.'migrations.yml';
        } elseif (file_exists($dir.'migrations.xml')) {
            return $dir.'migrations.xml';
        }

        return null;
    }
}