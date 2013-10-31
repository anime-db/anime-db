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
use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Config as ConfigManipulator;
use Composer\Package\Package;
use Symfony\Component\Finder\Finder;

/**
 * Config
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Config extends Job
{
    /**
     * Job priority
     *
     * @var integer
     */
    const PRIORITY = self::PRIORITY_INSTALL;

    /**
     * Manipulator
     *
     * @var \AnimeDb\Bundle\AnimeDbBundle\Manipulator\Config
     */
    protected $manipulator;

    /**
     * Construct
     *
     * @param \Composer\Package\Package $package
     */
    public function __construct(Package $package)
    {
        parent::__construct($package);
        $this->manipulator = new ConfigManipulator();
    }

    /**
     * Get the package config
     *
     * @return string|null
     */
    protected function getPackageConfig()
    {
        $finder = new Finder();
        $finder
            ->files()
            ->in(realpath(__DIR__.'/../../../../../../../vendor/'.$this->getPackage()->getName()))
            ->path('/\/Resources\/config\/([^\/]+\/)*config.(yml|xml)$/')
            ->notPath('/test/i')
            ->name('/^config.(yml|xml)$/');
        /* @var $file \SplFileInfo */
        foreach ($finder as $file) {
            $start = strrpos($file->getPathname(), '/Resources/config/');
            $path = substr($file->getPathname(), $start+strlen('/Resources/config/'));
            return $path;
        }
        return null;
    }
}