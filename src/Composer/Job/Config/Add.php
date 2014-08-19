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
use Symfony\Component\Finder\Finder;

/**
 * Job: Add package to config
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Add extends Job
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
            $this->getContainer()->getManipulator('config')
                ->addResource($bundle->getName(), $info['extension'], $path);
        }
    }

    /**
     * Get the package config
     *
     * @return string|null
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
        return null;
    }
}