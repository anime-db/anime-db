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
use Composer\Composer;
use Composer\Installer;

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
        $factory = new Factory();
        $io = new ConsoleIO($input, $output, $this->getHelperSet());
        $composer = $factory->createComposer($io);

        // search tag with new version of application
        $tag = $this->findNewVersion($composer->getPackage()->getVersion());
        if ($tag) {
            $this->doUpdateItself($tag, $composer, $output);
            // reload composer
            $composer = $factory->createComposer($io);
        } else {
            $output->writeln('<info>Application has already been updated to the latest version</info>');
        }

        $this->doUpdateComposer($composer, $io);
        $output->writeln('<info>Updating the application has been completed<info>');
    }

    /**
     * Get list of tags
     *
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
        $reg = '/^v?(?<version>\d+\.\d+\.\d+)(?:-(?:dev|patch|alpha|beta|rc)(?<suffix>\d+))?$/i';
        foreach ($this->getListTags() as $tag) {
            if (preg_match($reg, $tag['name'], $mat)) {
                $version = $mat['version'].'.'.(isset($mat['suffix']) ? $mat['suffix'] : '0');
                if (version_compare($version, $current_version) == 1) {
                    return array_merge(['version' => $version], $tag);
                } else {
                    break;
                }
            }
        }
        return false;
    }

    /**
     * Do update itself
     *
     * @param array $tag
     * @param \Composer\Composer $composer
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function doUpdateItself(array $tag, Composer $composer, OutputInterface $output)
    {
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
        $output->writeln('<info>Update itself has been completed</info>');
    }

    /**
     * Do update composer
     *
     * @param \Composer\Composer $composer
     * @param \Composer\IO\ConsoleIO $io
     */
    protected function doUpdateComposer(Composer $composer, ConsoleIO $io)
    {
        $lock_file = $this->getContainer()->getParameter('kernel.root_dir').'/../composer.lock';
        if (file_exists($lock_file)) {
            @unlink($lock_file);
        }
        $install = Installer::create($io, $composer)
            ->setDevMode(false)
            ->setPreferDist(true)
            ->setUpdate(true);

        if ($install->run() === 0) {
            $io->write('<info>Update requirements has been completed</info>');
        } else {
            $io->write('<error>During updating dependencies error occurred</error>');
        }
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
        // ignore errors during the removal of the old application
        try {
            $finder = Finder::create()
                ->files()
                ->ignoreUnreadableDirs()
                ->in($this->getContainer()->getParameter('kernel.root_dir').'/../src')
                ->in($this->getContainer()->getParameter('kernel.root_dir'))
                ->notPath('app/Resources')
                ->notPath('DoctrineMigrations');
            $fs->remove($finder);
        } catch (\Exception $e) {}

        // copy new version
        $this->copy($from, $this->getContainer()->getParameter('kernel.root_dir').'/../');

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
     * Conver file or list of files to iterator
     *
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