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
        $this->jobs[] = $job;
    }

    /**
     * Execute all jobs
     */
    public function execute()
    {
        /* @var $job \AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job */
        foreach ($this->jobs as $job) {
            $job->execute();
        }
    }
}