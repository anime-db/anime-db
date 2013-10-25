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
use Symfony\Component\Console\Input\InputArgument;
use Guzzle\Http\Client;
use Composer\Factory;
use Composer\IO\ConsoleIO;
use Composer\Package\Package;
use AnimeDb\Bundle\CatalogBundle\Event\Update\StoreEvents;
use AnimeDb\Bundle\CatalogBundle\Event\Update\ApplicationDownloaded;
use AnimeDb\Bundle\CatalogBundle\Event\Update\ApplicationUpdated;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Update Application
 *
 * @package AnimeDb\Bundle\CatalogBundle\Command
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class UpdateCommand extends ContainerAwareCommand
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::configure()
     */
    protected function configure()
    {
        $this->setName('animedb:update')
            ->setDescription('Update application');
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        // load composer
        $io = new ConsoleIO($input, $output, $this->getHelperSet());
        $factory = new Factory();
        $composer = $factory->createComposer($io);

        // search tag with new version of application
        $tag = $this->findNewVersion($composer->getPackage()->getPrettyVersion());
        if (!$tag) {
            $output->writeln('Application has already been updated to the latest version');
            return;
        }
        $output->writeln('Discovered a new version of the application: <info>'.$tag['version'].'</info>');

        // create install package
        $package = new Package('anime-db/anime-db', $tag['version'].'.0', $tag['version']);
        $package->setDistType('zip');
        $package->setDistUrl($tag['zipball_url']);
        $package->setInstallationSource('dist');

        // download new version
        $dm = $composer->getDownloadManager();
        $downloader = $dm->getDownloaderForInstalledPackage($package);
        $target = sys_get_temp_dir().'/anime-db';
        $downloader->download($package, $target);

        $dispatcher = $this->getContainer()->get('event_dispatcher');
        // notify about downloaded
        $dispatcher->dispatch(StoreEvents::APPLICATION_DOWNLOADED, new ApplicationDownloaded($target, $tag['version']));

        // rewriting the application files
        // TODO do rewriting

        // notify about updated
        $dispatcher->dispatch(StoreEvents::APPLICATION_UPDATED, new ApplicationUpdated($target, $tag['version']));
    }

    /**
     * @return array
     */
    protected function getListTags()
    {
        $client = new Client('https://api.github.com/');
        /* @var $response \Guzzle\Http\Message\Response */
        $response = $client->get('repos/anime-db/anime-db/tags')->send();
        return json_decode($response->getBody(true), true);
    }

    /**
     * Find new version
     *
     * @param string $current_version
     *
     * @return array|boolean
     */
    protected function findNewVersion($current_version)
    {
        // search tag with new version of application
        foreach ($this->getListTags() as $tag) {
            if (preg_match('/^v?(?<version>\d+\.\d+\.\d+)$/', $tag['name'], $mat)) {
                if (version_compare($mat['version'], $current_version) == 1) {
                    return array_merge(['version' => $mat['version']], $tag);
                } else {
                    // TODO it is only for dev
                    return array_merge(['version' => $mat['version']], $tag);
                    break;
                }
            }
        }
        return false;
    }
}