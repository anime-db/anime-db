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

/**
 * Job
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Job
{
    /**
     * Container
     *
     * @var \AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container
     */
    protected $container;

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
     * Execute job
     */
    abstract public function execute();
}