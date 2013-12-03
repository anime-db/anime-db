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

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;
use AnimeDb\Bundle\AnimeDbBundle\Composer\ScriptHandler;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Composer\Package\Package;
use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Routing manipulator
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Container
{
    /**
     * Kernel
     *
     * @var \AppKernel|null
     */
    private $kernel;

    /**
     * List of jobs
     *
     * @var array
     */
    protected $jobs = [];

    /**
     * Get kernel
     *
     * @return \AppKernel
     */
    public function getKernel()
    {
        if (!($this->kernel instanceof \AppKernel)) {
            if (isset($GLOBALS['loader']) && $GLOBALS['loader'] instanceof ClassLoader) {
                $GLOBALS['loader']->unregister();
            }
            $GLOBALS['loader'] = $this->getClassLoader();

            require_once __DIR__.'/../../../../../../app/AppKernel.php';
            $this->kernel = new \AppKernel('dev', true);
            $this->kernel->boot();
        }
        return $this->kernel;
    }

    /**
     * Get composer class loader
     *
     * @return \Composer\Autoload\ClassLoader
     */
    protected function getClassLoader()
    {
        $loader = new ClassLoader();
        $vendorDir = __DIR__.'/../../../../../../vendor';
        $baseDir = dirname($vendorDir);

        $map = require $vendorDir . '/composer/autoload_namespaces.php';
        foreach ($map as $namespace => $path) {
            $loader->set($namespace, $path);
        }

        $classMap = require $vendorDir . '/composer/autoload_classmap.php';
        if ($classMap) {
            $loader->addClassMap($classMap);
        }

        $loader->register(true);

        $includeFiles = require $vendorDir . '/composer/autoload_files.php';
        foreach ($includeFiles as $file) {
            require_once $file;
        }

        // intl
        if (!function_exists('intl_get_error_code')) {
            require_once $vendorDir.'/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';
            $loader->add('', $vendorDir.'/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs');
        }

        AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

        return $loader;
    }

    /**
     * Add job
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job $job
     */
    public function addJob(Job $job)
    {
        $job->setContainer($this);
        $this->jobs[$job->getPriority()][] = $job;
    }

    /**
     * Execute all jobs
     */
    public function execute()
    {
        // sort jobs by priority
        $jobs = [];
        ksort($this->jobs);
        foreach ($this->jobs as $priority_jobs) {
            $jobs = array_merge($jobs, $priority_jobs);
        }

        /* @var $job \AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job */
        foreach ($jobs as $job) {
            $job->execute();
        }
    }

    /**
     * Execute command
     *
     * @throws \RuntimeException
     *
     * @param string $cmd
     * @param integer $timeout
     */
    public function executeCommand($cmd, $timeout = 300)
    {
        $php = escapeshellarg($this->getPhp());
        $process = new Process($php.' app/console '.$cmd, __DIR__.'/../../../../../../', null, null, $timeout);
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });
        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf('An error occurred when executing the "%s" command.', $cmd));
        }
    }

    /**
     * Get path to php executable
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected function getPhp()
    {
        $phpFinder = new PhpExecutableFinder;
        if (!$phpPath = $phpFinder->find()) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }
        return $phpPath;
    }

    /**
     * Get packages options
     *
     * @param \Composer\Package\Package $package
     *
     * @return array
     */
    public function getPackageOptions(Package $package)
    {
        return array_merge(array(
            'anime-db-routing' => '',
            'anime-db-config' => '',
            'anime-db-bundle' => '',
            'anime-db-migrations' => '',
        ), $package->getExtra());
    }

    /**
     * Get the bundle from package
     *
     * For example package name 'demo-vendor/demo-bundle' converted to:
     *   \DemoVendor\Bundle\DemoBundle\DemoVendorDemoBundle
     *   \DemoVendor\Bundle\DemoBundle\DemoBundle
     *
     * @param \Composer\Package\Package $package
     *
     * @return string|null
     */
    public function getPackageBundle(Package $package)
    {
        $options = $this->getPackageOptions($package);
        // specific name
        if ($options['anime-db-bundle']) {
            return $options['anime-db-bundle'];
        }

        // package with the bundle can contain the word a 'bundle' in the name
        $name = preg_replace('/(\/.+)[^a-z]bundle$/i', '$1', $package->getName());
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
                $options['anime-db-bundle'] = $class;
                $package->setExtra($options);
                return $class;
            }
        }

        return null;
    }
}