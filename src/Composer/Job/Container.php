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
     * Root dir
     *
     * @var string
     */
    protected $root_dir;

    /**
     * Construct
     *
     * @param string $root_dir
     */
    public function __construct($root_dir)
    {
        $this->root_dir = $root_dir;
    }

    /**
     * Get event dispatcher
     *
     * @return \AnimeDb\Bundle\AnimeDbBundle\Event\Dispatcher
     */
    public function getEventDispatcher()
    {
        if (!($this->dispatcher instanceof Dispatcher)) {
            $this->dispatcher = new Dispatcher($this->root_dir);
        }
        return $this->dispatcher;
    }

    /**
     * Get manipulator
     *
     * @param string $name
     *
     * @return \AnimeDb\Bundle\AnimeDbBundle\Manipulator\ManipulatorInterface
     */
    public function getManipulator($name)
    {
        if (!isset($this->manipulators[$name])) {
            switch ($name) {
                case 'composer':
                    $this->manipulators[$name] = new ComposerManipulator($this->root_dir.'/../composer.json');
                    break;
                case 'config':
                    $this->manipulators[$name] = new ConfigManipulator($this->root_dir.'config/vendor_config.yml');
                    break;
                case 'kernel':
                    $this->manipulators[$name] = new KernelManipulator(
                        $this->root_dir.'bundles.php',
                        $this->root_dir.'AppKernel.php'
                    );
                    break;
                case 'routing':
                    $this->manipulators[$name] = new RoutingManipulator($this->root_dir.'config/routing.yml');
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
