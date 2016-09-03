<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself;

use Symfony\Component\EventDispatcher\Event;
use Composer\Package\RootPackageInterface;

/**
 * Event thrown when the application updated.
 */
class Updated extends Event
{
    /**
     * @var RootPackageInterface
     */
    protected $package;

    /**
     * @param RootPackageInterface $package
     */
    public function __construct(RootPackageInterface $package)
    {
        $this->package = $package;
    }

    /**
     * @return RootPackageInterface
     */
    public function getPackage()
    {
        return $this->package;
    }
}
