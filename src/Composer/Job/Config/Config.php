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
     * @param string $root_dir
     */
    public function __construct(Package $package, $root_dir = '')
    {
        parent::__construct($package, $root_dir);
        $this->manipulator = new ConfigManipulator($this->root_dir.'app/config/vendor_config.yml');
    }

    /**
     * Get the package config
     *
     * @return string|null
     */
    protected function getPackageConfig()
    {
        // specific location
        if ($config = $this->getPackageConfigFile()) {
            return $config;
        }

        $finder = new Finder();
        $finder
            ->files()
            ->in($this->getPackageDir())
            ->path('/\/Resources\/config\/([^\/]+\/)*config.(yml|xml)$/')
            ->name('/^config.(yml|xml)$/');

        // ignor configs in test
        if (stripos($this->getPackage()->getName(), 'test') === false) {
            $finder->notPath('/test/i');
        }

        /* @var $file \SplFileInfo */
        $prefix = DIRECTORY_SEPARATOR.'Resources'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR;
        foreach ($finder as $file) {
            $start = strrpos($file->getPathname(), $prefix);
            return substr($file->getPathname(), $start+strlen($prefix));
        }
        return null;
    }
}