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
     * Dispatcher driver
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $driver;

    /**
     * Store the event and dispatch it later
     *
     * @param string $event_name
     * @param \Symfony\Component\EventDispatcher\Event $event
     */
    public function dispatch($event_name, Event $event)
    {
        $dir = __DIR__.'/../../app/cache/dev/events/'.$event_name.'/';
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $event = serialize($event);
        file_put_contents($dir.md5($event).'.meta', $event);
    }

    /**
     * Shipping deferred events
     */
    public function shippingDeferredEvents()
    {
        if ($this->driver && file_exists(__DIR__.'/../../app/cache/dev/events/')) {
            $finder = new Finder();
            $finder->files()
                ->in(__DIR__.'/../../app/cache/dev/events/')
                ->name('*.meta');

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