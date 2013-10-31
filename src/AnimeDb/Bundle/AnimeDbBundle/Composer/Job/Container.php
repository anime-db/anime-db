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
use Composer\Package\PackageInterface;

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
            require __DIR__.'/../../../../../../app/bootstrap.php.cache';
            require __DIR__.'/../../../../../../app/AppKernel.php';
            $this->kernel = new \AppKernel('dev', true);
            $this->kernel->boot();
        }
        return $this->kernel;
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
     * @param \Composer\Package\PackageInterface $package
     *
     * @return array
     */
    public function getPackageOptions(PackageInterface $package)
    {
        return array_merge(array(
            'anime-db-routing' => '',
            'anime-db-config' => '',
            'anime-db-bundle' => '',
            'anime-db-migrations' => '',
        ), $package->getExtra());
    }
}