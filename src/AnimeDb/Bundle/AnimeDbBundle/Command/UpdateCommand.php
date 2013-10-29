<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Guzzle\Http\Client;
use Composer\Factory;
use Composer\IO\ConsoleIO;
use Composer\Package\Package;
use AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\StoreEvents;
use AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded;
use AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Updated;
use Symfony\Component\Filesystem\Filesystem;
use Composer\Package\Loader\ArrayLoader;
use Symfony\Component\Finder\Finder;

/**
 * Update Application
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Command
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
            ->setDescription('Update the application');
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
        $target = sys_get_temp_dir().'/anime-db';
        $fs = new Filesystem();
        $fs->remove($target);
        $composer->getDownloadManager()
            ->getDownloaderForInstalledPackage($package)
            ->download($package, $target);

        $new_package = $this->getPackage($target.'/composer.json');
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        // notify about downloaded
        $dispatcher->dispatch(StoreEvents::DOWNLOADED, new Downloaded($target, $new_package, $composer->getPackage()));

        // rewriting the application files
        $this->rewriting($target);

        // notify about updated
        $dispatcher->dispatch(StoreEvents::UPDATED, new Updated($new_package));
        $output->writeln('Updating the application has been completed');
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

    /**
     * Get package from config
     *
     * @param string $config
     *
     * @return array
     */
    protected function getPackage($config)
    {
        $config = file_get_contents($config);
        $config = json_decode($config, true);
        $loader = new ArrayLoader();
        return $loader->load($config);
    }

    /**
     * Rewrite the application files
     *
     * @param string $from
     */
    protected function rewriting($from)
    {
        $fs = new Filesystem();
        $target = realpath(__DIR__.'/../../../../../');
        // ignore errors during the removal of the old application
        try {
            // remove old source
            $fs->remove($target.'/src');
            $finder = Finder::create()
                ->files()
                ->ignoreUnreadableDirs()
                ->in($target.'/app')
                ->notPath('config/parameters.yml')
                ->notPath('Resources/anime.db');
            $fs->remove($finder);
        } catch (\Exception $e) {}

        // copy new version
        $this->copy($from, $target);

        // remove downloaded files
        $fs->remove($from);
    }

    /**
     * Copy files recursive
     *
     * @param string $src
     * @param string $dst
     */
    protected function copy($src, $dst) { 
        if (!is_array($src) && is_dir($src)) {
            $src = new \FilesystemIterator($src);
        }
        $files = iterator_to_array($this->toIterator($src));
        $files = array_reverse($files);
        $fs = new Filesystem();
        /* @var $file \SplFileInfo */
        foreach ($files as $file) {
            if (!file_exists($file) && !is_link($file)) {
                continue;
            }
            if (is_dir($file) && !is_link($file)) {
                $this->copy(new \FilesystemIterator($file), $dst.'/'.pathinfo($file, PATHINFO_BASENAME));
            } else {
                $fs->copy($file, $dst.'/'.pathinfo($file, PATHINFO_BASENAME), true);
            }
        }
    }

    /**
     * @param mixed $files
     *
     * @return \Traversable
     */
    protected function toIterator($files)
    {
        if (!$files instanceof \Traversable) {
            $files = new \ArrayObject(is_array($files) ? $files : array($files));
        }

        return $files;
    }
}