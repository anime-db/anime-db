<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing\Routing as BaseRouting;
use Symfony\Component\Finder\Finder;

/**
 * Job: Add package to routing
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Add extends BaseRouting
{
    /**
     * (non-PHPdoc)
     * @see AnimeDb\Bundle\AnimeDbBundle\Composer\Job.Job::execute()
     */
    public function execute()
    {
        $routing = $this->getPackageRouting();
        $bundle = $this->getPackageBundle();
        if ($routing && $bundle) {
            $bundle = new $bundle();
            $info = pathinfo($routing);
            $path = $info['dirname'] != '.' ? $info['dirname'].'/'.$info['filename'] : $info['filename'];
            $this->getContainer()->getManipulator('routing')
                ->addResource($this->getNodeName(), $bundle->getName(), $info['extension'], $path);
        }
    }

    /**
     * Get the package routing
     *
     * @return string
     */
    protected function getPackageRouting()
    {
        // This package has a file routing.xml, which contains the list of services,
        // rather than being contain the list of routers
        if ($this->getPackage()->getName() == 'sensio/framework-extra-bundle') {
            return '';
        }

        // specific location
        if ($routing = $this->getPackageOptionFile('anime-db-routing')) {
            return $routing;
        }

        $finder = new Finder();
        $finder
            ->files()
            ->in($this->getPackageDir())
            ->path('/\/Resources\/config\/([^\/]+\/)*routing.(yml|xml)$/')
            ->name('/^routing.(yml|xml)$/');

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