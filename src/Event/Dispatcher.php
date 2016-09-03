<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Event;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Dispatcher
{
    /**
     * Directory to store events.
     *
     * @var string
     */
    const EVENTS_DIR = '/cache/dev/events/';

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Directory to store events.
     *
     * @var string
     */
    protected $events_dir = '';

    /**
     * @param string $root_dir
     */
    public function __construct($root_dir)
    {
        $this->events_dir = $root_dir.self::EVENTS_DIR;
    }

    /**
     * Store the event and dispatch it later.
     *
     * @param string $event_name
     * @param Event $event
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

    public function shippingDeferredEvents()
    {
        if ($this->dispatcher && is_dir($this->events_dir)) {
            $finder = new Finder();
            $finder->files()->in($this->events_dir)->name('*.meta')->sortByName();

            /* @var $file SplFileInfo */
            foreach ($finder as $file) {
                $this->dispatcher->dispatch(
                    pathinfo($file->getPath(), PATHINFO_BASENAME),
                    unserialize(file_get_contents($file->getPathname()))
                );
                unlink($file->getPathname());
            }
        }
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcherDriver(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
}
