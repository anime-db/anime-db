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

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;
use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Routing as RoutingManipulator;
use Composer\Package\Package;
use Symfony\Component\Finder\Finder;

/**
 * Routing
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Routing extends Job
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
     * @var \AnimeDb\Bundle\AnimeDbBundle\Manipulator\Routing
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
        $this->manipulator = new RoutingManipulator();
    }

    /**
     * Get the package routing
     *
     * @return string|null
     */
    protected function getPackageRouting()
    {
        // This package has a file routing.xml, which contains the list of services,
        // rather than being contain the list of routers
        if ($this->getPackage()->getName() == 'sensio/framework-extra-bundle') {
            return null;
        }

        // specific location
        if ($routing = $this->getPackageRoutingFile()) {
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
        $prefix = DIRECTORY_SEPARATOR.'Resources'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR;
        foreach ($finder as $file) {
            $start = strrpos($file->getPathname(), $prefix);
            return substr($file->getPathname(), $start+strlen($prefix));
        }
        return null;
    }

    /**
     * Get the node name from the package name
     *
     * @return string
     */
    protected function getNodeName()
    {
        $name = strtolower($this->getPackage()->getName());
        // package with the bundle can contain the word a 'bundle' in the name
        $name = preg_replace('/(\/.+)[^a-z]bundle$/', '$1', $name);
        return preg_replace('/[^a-z_]+/', '_', $name);
    }
}