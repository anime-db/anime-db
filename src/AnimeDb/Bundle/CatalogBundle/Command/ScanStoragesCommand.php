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
use AnimeDb\Bundle\CatalogBundle\Event\Storage\UpdateItem;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\NewItem;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\DeleteItem;
use AnimeDb\Bundle\CatalogBundle\Entity\Notice;

/**
 * Scan storages for new items
 *
 * @package AnimeDb\Bundle\CatalogBundle\Command
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ScanStoragesCommand extends ContainerAwareCommand
{
    /**
     * Message for update item
     *
     * @var string
     */
    const MESSAGE_UPDATE_ITEM = 'Changes are detected in files of item %s';

    /**
     * Message for new item
     *
     * @var string
     */
    const MESSAGE_NEW_ITEM = 'Detected files for new item %s';

    /**
     * Message for delete item
     *
     * @var string
     */
    const MESSAGE_DELETE_ITEM = 'Files for item %s is removed';

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
        $em = $this->getContainer()->get('doctrine')->getManager();
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        /* @var $repository \AnimeDb\Bundle\CatalogBundle\Repository\Storage */
        $repository = $em->getRepository('AnimeDbCatalogBundle:Storage');
        $storages = $repository->getList(Storage::getTypesWritable());

        $start = microtime(true);
        $is_first_storage = true;

        /* @var $storage \AnimeDb\Bundle\CatalogBundle\Entity\Storage */
        foreach ($storages as $storage) {
            // create a visual space between storages
            if (!$is_first_storage) {
                $output->writeln('');
            }
            $is_first_storage = false;

            $output->writeln('Scan storage <info>'.$storage->getName().'</info>:');

            // storage is not modified
            if ($storage->getModified() && filemtime($storage->getPath()) == $storage->getModified()->getTimestamp()) {
                continue;
            }

            $finder = new Finder();
            $finder->in($storage->getPath());

            /* @var $file \Symfony\Component\Finder\SplFileInfo */
            foreach ($finder as $file) {
                /* @var $item \AnimeDb\Bundle\CatalogBundle\Entity\Item */
                foreach ($storage->getItems() as $item) {
                    if ($item->getPath() == $file->getPathname()) {
                        // item is exists and modified
                        if ($item->getDateUpdate()->getTimestamp() < $file->getPathInfo()->getMTime()) {
                            // send event
                            $dispatcher->dispatch(StoreEvents::UPDATE_ITEM, new UpdateItem($item));
                            // send notice
                            $notice = new Notice();
                            $notice->setMessage(sprintf(self::MESSAGE_UPDATE_ITEM, '"'.$item->getName().'"'));
                            $em->persist($notice);
                            // write output
                            $output->writeln(sprintf(self::MESSAGE_UPDATE_ITEM, '<info>'.$item->getName().'</info>'));
                        }
                        continue 2;
                    }
                }

                // it is a new item
                $name = $file->isDir() ? $file->getFilename() : pathinfo($file->getFilename(), PATHINFO_BASENAME);
                // send event
                $dispatcher->dispatch(StoreEvents::NEW_ITEM, new NewItem($storage, $file));
                // send notice
                $notice = new Notice();
                $notice->setMessage(sprintf(self::MESSAGE_NEW_ITEM, '"'.$name.'"'));
                $em->persist($notice);
                // write output
                $output->writeln(sprintf(self::MESSAGE_NEW_ITEM, '<info>'.$name.'</info>'));
            }

            // check of delete file for item
            foreach ($storage->getItems() as $item) {
                foreach ($finder as $file) {
                    if ($item->getPath() == $file->getPathname()) {
                        continue 2;
                    }
                }
                // send event
                $dispatcher->dispatch(StoreEvents::DELETE_ITEM, new DeleteItem($item));
                // send notice
                $notice = new Notice();
                $notice->setMessage(sprintf(self::MESSAGE_DELETE_ITEM, '"'.$item->getName().'"'));
                $em->persist($notice);
                // write output
                $output->writeln('<error>'.sprintf(self::MESSAGE_DELETE_ITEM, '"'.$item->getName().'"').'</error>');
            }

            // update date modified
            $storage->setModified(new \DateTime(filemtime($storage->getPath())));
            $em->persist($storage);
        }
        $output->writeln('');
        $output->writeln('Time: <info>'.round((microtime(true)-$start)*1000, 2).'</info> s.');

        $em->flush();
    }
}