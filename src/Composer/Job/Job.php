<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job;

use Composer\Package\Package;

abstract class Job
{
    /**
     * Preparation of \AppKernal to the initialization.
     *
     * @var int
     */
    const PRIORITY_INSTALL = 1;

    /**
     * Preparation of package to execute.
     *
     * @var int
     */
    const PRIORITY_INIT = 2;

    /**
     * Execute package.
     *
     * @var int
     */
    const PRIORITY_EXEC = 3;

    /**
     * Job priority.
     *
     * @var int
     */
    const PRIORITY = self::PRIORITY_EXEC;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Package
     */
    private $package;

    /**
     * @var string
     */
    protected $root_dir;

    /**
     * @param Package $package
     */
    public function __construct(Package $package)
    {
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
     * @param string $root_dir
     *
     * @return Job
     */
    public function setRootDir($root_dir)
    {
        $this->root_dir = $root_dir;

        return $this;
    }

    /**
     * @return string
     */
    public function getRootDir()
    {
        return $this->root_dir;
    }

    /**
     * @param Container $container
     *
     * @return Job
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return Package
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return static::PRIORITY;
    }

    /**
     * Execute job.
     */
    abstract public function execute();

    /**
     * Handle on register job in container.
     */
    public function register()
    {
    }

    /**
     * @param string $option
     *
     * @return string
     */
    protected function getPackageOption($option)
    {
        $options = $this->package->getExtra();

        if (isset($options[$option])) {
            return $options[$option];
        }

        return '';
    }

    /**
     * @param string $option
     *
     * @return string
     */
    protected function getPackageOptionFile($option)
    {
        $option = $this->getPackageOption($option);

        if ($option && file_exists($this->getPackageDir().$option)) {
            return $option;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getPackageDir()
    {
        return $this->root_dir.'vendor/'.$this->package->getName().'/';
    }

    /**
     * Get the bundle from package.
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
            '\\'.$vendor.'\Bundle\\'.$bundle.'Bundle\\'.$bundle.'Bundle',
        ];

        // vendor name can be contained in the bundle name
        // knplabs/knp-menu-bundle -> \Knp\Bundle\MenuBundle\KnpMenuBundle
        list(, $bundle) = explode('/', $name, 2);
        $bundle = trim($bundle);
        if (($pos = strpos($bundle, ' ')) !== false) {
            $vendor = substr($bundle, 0, $pos);
            $bundle = str_replace(' ', '', substr($bundle, $pos + 1));
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

        return;
    }

    /**
     * Get a simple copy of the package.
     *
     * @return Package
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

    /**
     * Get the routing node name from the package name.
     *
     * @return string
     */
    protected function getRoutingNodeName()
    {
        $name = strtolower($this->getPackage()->getName());
        // package with the bundle can contain the word a 'bundle' in the name
        $name = preg_replace('/(\/.+)[^a-z]bundle$/', '$1', $name);
        $name = preg_replace('/[^a-z_]+/', '_', $name);

        return $name;
    }
}
