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
use Symfony\Component\Finder\Finder;

/**
 * Job: Add package to config
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Add extends AddConfig
{
    /**
     * Add config
     *
     * @param string $bundle
     * @param string $extension
     * @param string $path
     */
    protected function addConfig($bundle, $extension, $path)
    {
        $this->getContainer()->getManipulator('config')->addResource($bundle, $extension, $path);
    }

    /**
     * Get the package config
     *
     * @return string
     */
    protected function getPackageConfig()
    {
        // specific location
        if ($config = $this->getPackageOptionFile('anime-db-config')) {
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
        foreach ($finder as $file) {
            $path = str_replace(DIRECTORY_SEPARATOR, '/', $file->getPathname());
            return substr($path, strrpos($path, '/Resources/config/'));
        }
        return '';
    }
}