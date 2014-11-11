<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Event;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Finder\Finder;

/**
 * Event dispatcher
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Event
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Dispatcher
{
    /**
     * Directory to store events
     *
     * @var string
     */
    const EVENTS_DIR = '/cache/dev/events/';

    /**
     * Dispatcher driver
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $driver;

    /**
     * Directory to store events
     *
     * @var string
     */
    protected $events_dir = '';

    /**
     * Construct
     *
     * @param string $root_dir
     */
    public function __construct($root_dir)
    {
        $this->events_dir = $root_dir.self::EVENTS_DIR;
    }

    /**
     * Store the event and dispatch it later
     *
     * @param string $event_name
     * @param \Symfony\Component\EventDispatcher\Event $event
     */
    public function dispatch($event_name, Event $event)
    {
        $dir = $this->events_dir.$event_name.'/';
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        $event = serialize($event);
        file_put_contents($dir.md5($event).'.meta', $event);
    }

    /**
     * Shipping deferred events
     */
    public function shippingDeferredEvents()
    {
        if ($this->driver && is_dir($this->events_dir)) {
            $finder = new Finder();
            $finder->files()->in($this->events_dir)->name('*.meta')->sortByName();

            /* @var $file \Symfony\Component\Finder\SplFileInfo */
            foreach ($finder as $file) {
                $this->driver->dispatch(
                    pathinfo($file->getPath(), PATHINFO_BASENAME),
                    unserialize(file_get_contents($file->getPathname()))
                );
                unlink($file->getPathname());
            }
        }
    }

    /**
     * Set dispatcher driver
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $driver
     */
    public function setDispatcherDriver(EventDispatcherInterface $driver)
    {
        $this->driver = $driver;
    }
}
