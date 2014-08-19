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
use AnimeDb\Bundle\AnimeDbBundle\Event\Dispatcher;
use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Composer as ComposerManipulator;
use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Config as ConfigManipulator;
use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Kernel as KernelManipulator;
use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Routing as RoutingManipulator;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

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
     * List manipulators
     *
     * @var array
     */
    private $manipulators = [];

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
     * Get manipulator
     *
     * @param string $name
     *
     * @return \AnimeDb\Bundle\AnimeDbBundle\Manipulator\Manipulator
     */
    public function getManipulator($name)
    {
        if (!isset($this->manipulators[$name])) {
            $root_dir = __DIR__.'/../../../';
            switch ($name) {
                case 'composer':
                    $this->manipulators[$name] = new ComposerManipulator($root_dir.'composer.json');
                    break;
                case 'config':
                    $this->manipulators[$name] = new ConfigManipulator($root_dir.'app/config/vendor_config.yml');
                    break;
                case 'kernel':
                    $this->manipulators[$name] = new KernelManipulator(
                        $root_dir.'app/bundles.php',
                        $root_dir.'app/AppKernel.php'
                    );
                    break;
                case 'routing':
                    $this->manipulators[$name] = new RoutingManipulator($root_dir.'app/config/routing.yml');
                    break;
                default:
                    throw new \InvalidArgumentException('Unknown manipulator: '.$name);
            }
        }
        return $this->manipulators[$name];
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
        $job->register();
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
        $process = new Process($php.' app/console '.$cmd, __DIR__.'/../../../', null, null, $timeout);
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
}