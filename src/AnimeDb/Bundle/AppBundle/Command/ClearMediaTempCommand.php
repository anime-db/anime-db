<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Clear the media temporary folder of images
 *
 * @package AnimeDb\Bundle\AppBundle\Command
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ClearMediaTempCommand extends ContainerAwareCommand
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::configure()
     */
    protected function configure()
    {
        $this->setName('animedb:clear-media-temp')
            ->setDescription('Clear the media temporary folder of images');
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $start = microtime(true);

        if (file_exists($dir = $this->getContainer()->getParameter('kernel.root_dir').'/../web/media/tmp/')) {
            $fs = new Filesystem();

            $finder = new Finder();
            $finder->in($dir)->date('< 1 hour ago')->ignoreUnreadableDirs();
            /* @var $file \SplFileInfo */
            foreach ($finder as $file) {
                $fs->remove($file->getRealPath());
            }
        }

        $output->writeln('Time: <info>'.round((microtime(true)-$start)*1000, 2).'</info> s.');
    }
}