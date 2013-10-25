<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer;

use Composer\Script\PackageEvent;
use Composer\Script\CommandEvent;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Composer\Script\Event;
use Composer\Package\PackageInterface;
use Symfony\Component\Finder\Finder;
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\StoreEvents;
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Installed;
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Removed;

/**
 * Composer script handler
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ScriptHandler
{
    /**
     * List events
     *
     * @var array
     */
    protected static $events = [];

    /**
     * Add package to AppKernel
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function addPackageToAppKernel(PackageEvent $event)
    {
        // TODO write PackageBundle class into app/AppKernel
        // @see \Sensio\Bundle\GeneratorBundle\Manipulator\KernelManipulator
    }

    /**
     * Remove packages from AppKernel
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function removePackageFromAppKernel(PackageEvent $event)
    {
        // TODO remove PackageBundle class from app/AppKernel
        // @see \Sensio\Bundle\GeneratorBundle\Manipulator\KernelManipulator
    }

    /**
     * Migrate packages
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function migratePackage(PackageEvent $event)
    {
        $command = 'doctrine:migrations:migrate --no-interaction';

        /* @var $package \Composer\Package\PackageInterface */
        switch ($event->getOperation()->getJobType()) {
            case 'uninstall':
                $command .= ' 0'; // migration to first version
            case 'install':
                $package = $event->getOperation()->getPackage();
                break;
            case 'update':
                $package = $event->getOperation()->getTargetPackage();
        }

        // migrate only packages
        if ($package->getType() != 'anime-db-plugin') {
           return;
        }

        if ($config = self::getMigrationsConfig($package)) {
            self::executeCommand($event, $command.' --configuration='.$config, null);
        }
    }

    /**
     * Add packages to routing
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function addPackageToRouting(PackageEvent $event)
    {
        // TODO add @PackageBundle/Resources/config/routing.yml into app/config/routing.yml
        // @see \Sensio\Bundle\GeneratorBundle\Manipulator\RoutingManipulator
    }

    /**
     * Remove packages from routing
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function removePackageFromRouting(PackageEvent $event)
    {
        // TODO remove @PackageBundle/Resources/config/routing.yml from app/config/routing.yml
        // @see \Sensio\Bundle\GeneratorBundle\Manipulator\RoutingManipulator
    }

    /**
     * Notify listeners that the package has been installed
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function installPackageNotify(PackageEvent $event)
    {
        switch ($event->getOperation()->getJobType()) {
            case 'install':
                $package = $event->getOperation()->getPackage();
                break;
            case 'update':
                $package = $event->getOperation()->getTargetPackage();
        }
        self::$events[] = [StoreEvents::INSTALLED, $package];
    }

    /**
     * Notify listeners that the package has been removed
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function removePackageNotify(PackageInterface $package)
    {
        self::$events[] = [StoreEvents::REMOVED, $event->getOperation()->getPackage()];
    }

    /**
     * Get path to migrations config file from package
     *
     * @param \Composer\Package\PackageInterface $package
     *
     * @return string|boolean
     */
    protected static function getMigrationsConfig(PackageInterface $package)
    {
        $options = self::getPackageOptions($package);
        // specific location
        if ($options['anime-db-migrations']) {
            return $options['anime-db-migrations'];
        }

        $finder = new Finder();
        $finder->files()
            ->in(__DIR__.'/../../../../../vendor/'.$package->getName())
            ->name('/^migrations\.(yml|xml)$/');

        /* @var $file \SplFileInfo */
        foreach ($finder as $file) {
            return $file->getRealPath();
        }
        return false;
    }

    /**
     * Execute command
     *
     * @throws \RuntimeException
     *
     * @param \Composer\Script\Event $event
     * @param string $cmd
     * @param integer $timeout
     */
    protected static function executeCommand(Event $event, $cmd, $timeout = 300)
    {
        $php = escapeshellarg(self::getPhp());
        $console = 'app/console';
        if ($event->getIO()->isDecorated()) {
            $console .= ' --ansi';
        }

        $process = new Process($php.' '.$console.' '.$cmd, __DIR__.'/../../../../../', null, null, $timeout);
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });
        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf('An error occurred when executing the "%s" command.', $cmd));
        }
    }

    /**
     * Get packages options
     *
     * @param \Composer\Package\PackageInterface $package
     *
     * @return array
     */
    protected static function getPackageOptions(PackageInterface $package)
    {
        return array_merge(array(
            'anime-db-routing' => '',
            'anime-db-migrations' => '',
        ), $package->getExtra());
    }

    /**
     * Get path to php executable
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected static function getPhp()
    {
        $phpFinder = new PhpExecutableFinder;
        if (!$phpPath = $phpFinder->find()) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }
        return $phpPath;
    }

    /**
     * Global migrate
     *
     * @param \Composer\Script\CommandEvent $event
     */
    public static function migrate(CommandEvent $event)
    {
        self::executeCommand($event, 'doctrine:migrations:migrate --no-interaction');
    }

    /**
     * Notify listeners and subscribers that the package has been installed or removed
     *
     * @param \Composer\Script\CommandEvent $event
     */
    public static function notify(CommandEvent $event)
    {
        if (self::$events) {
            // load kernel
            require_once __DIR__.'/../../../../../app/bootstrap.php.cache';
            require_once __DIR__.'/../../../../../app/AppKernel.php';
            $kernel = new \AppKernel('dev', true);
            $kernel->boot();

            // notify listeners
            $dispatcher = $kernel->getContainer()->get('event_dispatcher');
            foreach (self::$events as $event) {
                switch ($event[0]) {
                    case StoreEvents::INSTALLED:
                        $event[1] = new Installed($event[1]);
                        break;
                    case StoreEvents::REMOVED:
                        $event[1] = new Removed($event[1]);
                        break;
                }
                $dispatcher->dispatch($event[0], $event[1]);
            }
        }
    }
}