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
use AnimeDb\Bundle\AnimeDbBundle\Event\Dispatcher;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Composer\Package\Package;

/**
 * Routing manipulator
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Container
{
    /**
     * Event dispatcher
     *
     * @var \AnimeDb\Bundle\AnimeDbBundle\Event\Dispatcher|null
     */
    private $dispatcher;

    /**
     * List of jobs
     *
     * @var array
     */
    protected $jobs = [];

    /**
     * PHP path
     *
     * @var string|null|false
     */
    protected $php_path = null;

    /**
     * Get event dispatcher
     *
     * @return \AnimeDb\Bundle\AnimeDbBundle\Event\Dispatcher
     */
    public function getEventDispatcher()
    {
        if (!($this->dispatcher instanceof Dispatcher)) {
            $this->dispatcher = new Dispatcher();
        }
        return $this->dispatcher;
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
        if (is_null($this->php_path)) {
            $finder = new PhpExecutableFinder();
            if (!($this->php_path = $finder->find())) {
                throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
            }
        }
        return $this->php_path;
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

    /**
     * Get a simple copy of the packet
     *
     * @param \Composer\Package\Package $package
     *
     * @return \Composer\Package\Package
     */
    public function getSimpleCopyOfPacket(Package $package)
    {
        $copy = new Package($package->getName(), $package->getVersion(), $package->getVersion());
        $copy->setType($package->getType());
        $copy->setExtra($package->getExtra());
        return $copy;
    }
}