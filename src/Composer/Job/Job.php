<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job;

use Composer\Package\Package;

/**
 * Job
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Job
{
    /**
     * Preparation of \AppKernal to the initialization
     *
     * @var integer
     */
    const PRIORITY_INSTALL = 1;

    /**
     * Preparation of package to execute
     *
     * @var integer
     */
    const PRIORITY_INIT = 2;

    /**
     * Execute package
     *
     * @var integer
     */
    const PRIORITY_EXEC = 3;

    /**
     * Job priority
     *
     * @var integer
     */
    const PRIORITY = self::PRIORITY_EXEC;

    /**
     * Container
     *
     * @var \AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container
     */
    private $container;

    /**
     * Package
     *
     * @var \Composer\Package\Package
     */
    private $package;

    /**
     * Root dir
     *
     * @var string
     */
    protected $root_dir;

    /**
     * Construct
     *
     * @param \Composer\Package\Package $package
     * @param string $root_dir
     */
    public function __construct(Package $package, $root_dir = '')
    {
        $this->root_dir = $root_dir ?: __DIR__.'/../../../';
        $this->package = $package;
        $this->package->setExtra(array_merge([
                'anime-db-routing' => '',
                'anime-db-config' => '',
                'anime-db-bundle' => '',
                'anime-db-migrations' => '',
            ],
            $this->package->getExtra()
        ));
    }

    /**
     * Set container
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get container
     *
     * @return \AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Get package
     *
     * @return \Composer\Package\Package
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * Get job priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return static::PRIORITY;
    }

    /**
     * Execute job
     */
    abstract public function execute();

    /**
     * Handle on register job in container
     */
    public function register()
    {
    }

    /**
     * Get package option
     *
     * @param string $option
     *
     * @return string
     */
    private function getPackageOption($option)
    {
        $options = $this->package->getExtra();
        if (isset($options[$option])) {
            return $options[$option];
        }
        return null;
    }

    /**
     * Get package option file
     *
     * @param string $option
     *
     * @return string
     */
    private function getPackageOptionFile($option)
    {
        $option = $this->getPackageOption($option);
        if ($option && file_exists($this->getPackageDir().$option)) {
            return $option;
        }
        return '';
    }

    /**
     * Get package migrations config file
     *
     * @return string
     */
    public function getPackageMigrationsFile()
    {
        return $this->getPackageOptionFile('anime-db-migrations');
    }

    /**
     * Get package config file
     *
     * @return string
     */
    public function getPackageConfigFile()
    {
        return $this->getPackageOptionFile('anime-db-config');
    }

    /**
     * Get package routing config file
     *
     * @return string
     */
    public function getPackageRoutingFile()
    {
        return $this->getPackageOptionFile('anime-db-routing');
    }

    /**
     * Get packages directory
     *
     * @return string
     */
    public function getPackageDir()
    {
        return $this->root_dir.'vendor/'.$this->package->getName().'/';
    }

    /**
     * Get the bundle from package
     *
     * For example package name 'demo-vendor/foo-bar-bundle' converted to:
     *   \DemoVendor\Bundle\FooBarBundle\DemoVendorFooBarBundle
     *   \DemoVendor\Bundle\FooBarBundle\FooBarBundle
     *   \Foo\Bundle\BarBundle\FooBarBundle
     *   \Foo\Bundle\BarBundle\BarBundle
     *
     * @return string|null
     */
    public function getPackageBundle()
    {
        // specific name
        if (($class = $this->getPackageOption('anime-db-bundle')) && class_exists($class)) {
            return $class;
        }

        // package with the bundle can contain the word a 'bundle' in the name
        $name = preg_replace('/(\/.+)[^a-z]bundle$/i', '$1', $this->package->getName());
        // convert package name to bundle name
        $name = preg_replace('/[^a-zA-Z\/]+/', ' ', $name);
        $name = ucwords(str_replace('/', ' / ', $name));
        list($vendor, $bundle) = explode('/', str_replace(' ', '', $name), 2);

        $classes = [
            '\\'.$vendor.'\Bundle\\'.$bundle.'Bundle\\'.$vendor.$bundle.'Bundle',
            '\\'.$vendor.'\Bundle\\'.$bundle.'Bundle\\'.$bundle.'Bundle'
        ];

        // vendor name can be contained in the bundle name
        // knplabs/knp-menu-bundle -> \Knp\Bundle\MenuBundle\KnpMenuBundle
        list(, $bundle) = explode('/', $name, 2);
        $bundle = trim($bundle);
        if (($pos = strpos($bundle, ' ')) !== false) {
            $vendor = substr($bundle, 0, $pos);
            $bundle = str_replace(' ', '', substr($bundle, $pos+1));
            $classes[] = '\\'.$vendor.'\Bundle\\'.$bundle.'Bundle\\'.$vendor.$bundle.'Bundle';
            $classes[] = '\\'.$vendor.'\Bundle\\'.$bundle.'Bundle\\'.$bundle.'Bundle';
        }

        foreach ($classes as $class) {
            if (class_exists($class)) {
                // cache the bundle class
                $options = $this->package->getExtra();
                $options['anime-db-bundle'] = $class;
                $this->package->setExtra($options);

                return $class;
            }
        }

        return null;
    }

    /**
     * Get a simple copy of the package
     *
     * @return \Composer\Package\Package
     */
    public function getPackageCopy()
    {
        $copy = new Package(
            $this->package->getName(),
            $this->package->getVersion(),
            $this->package->getPrettyVersion()
        );
        $copy->setType($this->package->getType());
        $copy->setExtra($this->package->getExtra());
        return $copy;
    }
}