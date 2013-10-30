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
use Composer\Package\PackageInterface;

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
     * @var \Composer\Package\PackageInterface
     */
    protected $package;

    /**
     * Construct
     *
     * @param \Composer\Package\PackageInterface $package
     */
    public function __construct(PackageInterface $package)
    {
        $this->package = $package;
    }

    /**
     * Get path to migrations config file from package
     *
     * @return string|boolean
     */
    protected function getMigrationsConfig()
    {
        $options = array_merge(['anime-db-migrations' => ''], $this->package->getExtra());
        // specific location
        if ($options['anime-db-migrations']) {
            return $options['anime-db-migrations'];
        }

        $finder = new Finder();
        $finder->files()
            ->in(__DIR__.'/../../../../../../../vendor/'.$this->package->getName())
            ->name('/^migrations\.(yml|xml)$/');

        /* @var $file \SplFileInfo */
        foreach ($finder as $file) {
            return $file->getRealPath();
        }
        return false;
    }
}