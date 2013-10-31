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
     * Construct
     *
     * @param \Composer\Package\Package $package
     */
    public function __construct(Package $package)
    {
        $this->package = $package;
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
    public static function getPriority()
    {
        return static::PRIORITY;
    }

    /**
     * Execute job
     */
    abstract public function execute();
}