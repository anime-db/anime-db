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
use Guzzle\Http\Client;
use Composer\Factory;
use Composer\IO\ConsoleIO;
use Composer\Package\Package;
use AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\StoreEvents;
use AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded;
use AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Updated;
use AnimeDb\Bundle\AnimeDbBundle\Event\Dispatcher;
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
        $io = new ConsoleIO($input, $output, $this->getHelperSet());
        $composer = $this->createComposer($io);
        /* @var $github \AnimeDb\Bundle\AnimeDbBundle\Client\GitHub */
        $github = $this->getContainer()->get('anime_db.client.github');

        // search tag with new version of application
        $output->writeln('Search for a new version of the application');
        $tag = $github->getLastRelease('anime-db/anime-db');
        $tag['version'] = $github->getVersionCompatible($tag['name']);
        $current_version = $github->getVersionCompatible($composer->getPackage()->getPrettyVersion());

        if ($tag && version_compare($tag['version'], $current_version) == 1) {
            $this->doUpdateItself($tag, $composer, $output);
            // reload composer
            $composer = $this->createComposer($io);
        } else {
            $output->writeln('<info>Application has already been updated to the latest version</info>');
        }
        unset($github, $tag, $current_version);

        $this->doUpdateComposer($composer, $io);
        $output->writeln('<info>Updating the application has been completed<info>');
    }

    /**
     * Create new composer object
     *
     * @param \Composer\IO\ConsoleIO $io
     *
     * @return \Composer\Composer
     */
    protected function createComposer(ConsoleIO $io)
    {
        // update application components to the latest version
        $lock_file = $this->getContainer()->getParameter('kernel.root_dir').'/../composer.lock';
        if (file_exists($lock_file)) {
            @unlink($lock_file);
        }
        return (new Factory())->createComposer($io);
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
        $output->writeln('Discovered a new version of the application: <info>'.$tag['name'].'</info>');

        // create install package
        $package = new Package('anime-db/anime-db', $tag['version'], $tag['name']);
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
        // event will be sent after the update application components
        $dispatcher = new Dispatcher();
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
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $composer->getDownloadManager()->setOutputProgress(false);
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
     * @return \Composer\Package\RootPackage
     */
    protected function getPackage($config)
    {
        $config = json_decode(file_get_contents($config), true);
        $loader = new ArrayLoader();
        return $loader->load($config, 'Composer\Package\RootPackage');
    }

    /**
     * Rewrite the application files
     *
     * @param string $from
     */
    protected function rewriting($from)
    {
        $fs = new Filesystem();
        try {
            $finder = Finder::create()
                ->files()
                ->ignoreUnreadableDirs()
                ->in($this->getContainer()->getParameter('kernel.root_dir').'/../src')
                ->in($this->getContainer()->getParameter('kernel.root_dir'))
                ->notPath('app/DoctrineMigrations')
                ->notPath('app/Resources')
                ->notPath('app/bootstrap.php.cache');
            $fs->remove($finder);
        } catch (\Exception $e) {} // ignore errors during the removal of the old application

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