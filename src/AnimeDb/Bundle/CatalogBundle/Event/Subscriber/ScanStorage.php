<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\StoreEvents;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\DeleteItemFiles;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\DetectedNewFiles;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\UpdateItemFiles;
use AnimeDb\Bundle\CatalogBundle\Entity\Notice;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\TwigBundle\TwigEngine;
use AnimeDb\Bundle\CatalogBundle\Plugin\Search\Chain as SearchChain;

/**
 * Storages scan subscriber
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Subscriber
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ScanStorage implements EventSubscriberInterface
{
    /**
     * Entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Templating
     *
     * @var \Symfony\Bundle\TwigBundle\TwigEngine
     */
    private $templating;

    /**
     * Search chain
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Plugin\Search\Chain
     */
    private $search;

    /**
     * Construct
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Symfony\Bundle\TwigBundle\TwigEngine $templating
     */
    public function __construct(EntityManager $em, TwigEngine $templating, SearchChain $search)
    {
        $this->em = $em;
        $this->templating = $templating;
        $this->search = $search;
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\EventDispatcher.EventSubscriberInterface::getSubscribedEvents()
     */
    public static function getSubscribedEvents()
    {
        return [
            StoreEvents::DELETE_ITEM_FILES => 'onDeleteItemFiles',
            StoreEvents::DETECTED_NEW_FILES => 'onDetectedNewFiles',
            StoreEvents::UPDATE_ITEM_FILES => 'onUpdateItemFiles',
        ];
    }

    /**
     * On delete item files
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Storage\DeleteItemFiles $event
     */
    public function onDeleteItemFiles(DeleteItemFiles $event)
    {
        $notice = new Notice();
        $notice->setMessage($this->templating->render(
            'AnimeDbCatalogBundle:Notice:massages/delete_item_files.html.twig',
            ['item' => $event->getItem()]
        ));
        $this->em->persist($notice);
    }

    /**
     * On detected new files
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Storage\DetectedNewFiles $event
     */
    public function onDetectedNewFiles(DetectedNewFiles $event)
    {
        if ($event->getFile()->isDir()) {
            $name = $event->getFile()->getFilename();
        } else {
            $name = pathinfo($event->getFile()->getFilename(), PATHINFO_BASENAME);
        }

        // default search plugin
        if ($plugin = $this->search->getDafeultPlugin()) {
            $plugin = $plugin->getName();
        }

        $notice = new Notice();
        $notice->setMessage($this->templating->render(
            'AnimeDbCatalogBundle:Notice:massages/detected_new_files.html.twig',
            ['item' => $name, 'storage' => $event->getStorage(), 'plugin' => $plugin]
        ));
        $this->em->persist($notice);
    }

    /**
     * On update item files
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Storage\UpdateItemFiles $event
     */
    public function onUpdateItemFiles(UpdateItemFiles $event)
    {
        $notice = new Notice();
        $notice->setMessage($this->templating->render(
            'AnimeDbCatalogBundle:Notice:massages/update_item_files.html.twig',
            ['item' => $event->getItem()]
        ));
        $this->em->persist($notice);
    }
}