<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use Symfony\Component\Finder\Finder;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\StoreEvents;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\UpdateItemFiles;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\DetectedNewFiles;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\DeleteItemFiles;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Scan storages for new items
 *
 * @package AnimeDb\Bundle\CatalogBundle\Command
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ScanStoragesCommand extends ContainerAwareCommand
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::configure()
     */
    protected function configure()
    {
        $this->setName('animedb:scan-storage')
            ->setDescription('Scan storages for new items');
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        $start = microtime(true);

        $storages = $em->getRepository('AnimeDbCatalogBundle:Storage')
            ->getList(Storage::getTypesWritable());

        /* @var $storage \AnimeDb\Bundle\CatalogBundle\Entity\Storage */
        foreach ($storages as $storage) {
            $output->writeln('');
            $output->writeln('Scan storage <info>'.$storage->getName().'</info>:');

            // storage is exists and not modified
            if (!file_exists($storage->getPath()) ||
                ($storage->getModified() && filemtime($storage->getPath()) == $storage->getModified()->getTimestamp())
            ) {
                continue;
            }

            $finder = new Finder();
            $finder->in($storage->getPath());

            /* @var $file \Symfony\Component\Finder\SplFileInfo */
            foreach ($finder as $file) {
                if ($item = $this->getItemOfUpdatedFiles($storage, $file)) {
                    $dispatcher->dispatch(StoreEvents::UPDATE_ITEM_FILES, new UpdateItemFiles($item));
                    $output->writeln('Changes are detected in files of item <info>'.$item->getName().'</info>');
                } else {
                    // it is a new item
                    $name = $file->isDir() ? $file->getFilename() : pathinfo($file->getFilename(), PATHINFO_BASENAME);
                    $dispatcher->dispatch(StoreEvents::DETECTED_NEW_FILES, new DetectedNewFiles($storage, $file));
                    $output->writeln('Detected files for new item <info>'.$name.'</info>');
                }
            }

            // check of delete file for item
            foreach ($this->getItemsOfDeletedFiles($storage, $finder) as $item) {
                $dispatcher->dispatch(StoreEvents::DELETE_ITEM_FILES, new DeleteItemFiles($item));
                $output->writeln('<error>Files for item "'.$item->getName().'" is removed</error>');
            }

            // update date modified
            $storage->setModified(new \DateTime(date('Y-m-d H:i:s', filemtime($storage->getPath()))));
            $em->persist($storage);
        }
        $em->flush();

        $output->writeln('');
        $output->writeln('Time: <info>'.round((microtime(true)-$start)*1000, 2).'</info> s.');
    }

    /**
     * Get items of deleted files
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     * @param \Symfony\Component\Finder\Finder $finder
     *
     * @return array
     */
    protected function getItemsOfDeletedFiles(Storage $storage, Finder $finder)
    {
        $items = [];
        // check of delete file for item
        foreach ($storage->getItems() as $item) {
            foreach ($finder as $file) {
                if ($item->getPath() == $file->getPathname()) {
                    continue 2;
                }
            }
            $items[] = $item;
        }
        return $items;
    }

    /**
     * Get item of updated files
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     * @param \Symfony\Component\Finder\SplFileInfo $file
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item|boolean
     */
    protected function getItemOfUpdatedFiles(Storage $storage, SplFileInfo $file)
    {
        /* @var $item \AnimeDb\Bundle\CatalogBundle\Entity\Item */
        foreach ($storage->getItems() as $item) {
            if ($item->getPath() == $file->getPathname()) {
                // item is exists and modified
                if ($item->getDateUpdate()->getTimestamp() < $file->getPathInfo()->getMTime()) {
                    return $item;
                }
                return false;
            }
        }
        return false;
    }
}