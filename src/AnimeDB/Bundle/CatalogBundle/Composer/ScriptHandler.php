<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Composer;

use Composer\Script\PackageEvent;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Composer\Script\Event;
use Composer\Package\PackageInterface;

/**
 * Composer script handler
 *
 * @package AnimeDB\Bundle\CatalogBundle\Composer
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ScriptHandler
{
    /**
     * Add plugin to AppKernel
     *
     * @param \Composer\Script\CommandEvent $event
     */
    public static function addPluginToAppKernel(PackageEvent $event)
    {
        // TODO write PluginBundle class into app/AppKernel
    }

    /**
     * Remove plugin from AppKernel
     *
     * @param \Composer\Script\CommandEvent $event
     */
    public static function removePluginFromAppKernel(PackageEvent $event)
    {
        // TODO remove PluginBundle class from app/AppKernel
    }

    /**
     * Migrate plugin
     *
     * @param PackageEvent $event
     */
    public static function migratePlugin(PackageEvent $event)
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

        // migrate only plugin
        if ($package->getType() != 'anime-db-plugin') {
           return;
        }

        if ($config = self::getMigrationsConfig($package)) {
            self::executeCommand($event, $command.' --configuration='.$config);
        }
    }

    /**
     * Add plugin to routing
     *
     * @param \Composer\Script\CommandEvent $event
     */
    public static function addPluginToRouting(PackageEvent $event)
    {
        // TODO add @PluginBundle/Resources/config/routing.yml into app/config/routing.yml
    }

    /**
     * Remove plugin from routing
     *
     * @param \Composer\Script\CommandEvent $event
     */
    public static function removePluginFromRouting(PackageEvent $event)
    {
        // TODO remove @PluginBundle/Resources/config/routing.yml from app/config/routing.yml
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
        $options = self::getPluginOptions($package);
        // specific location
        if ($options['anime-db-migrations']) {
            return $options['anime-db-migrations'];
        }

        if ($dir = self::getBundleRootDir($package)) {
            // get path to migrations config
            $dir .= '/Resources/config/';
            if (file_exists($dir.'migrations.yml')) {
                return $dir.'migrations.yml';
            } elseif (file_exists($dir.'migrations.xml')) {
                return $dir.'migrations.xml';
            }
        }
        return false;
    }

    /**
     * Get bundle class from package
     *
     * @param \Composer\Package\PackageInterface $package
     *
     * @return string
     */
    protected static function getBundleClass(PackageInterface $package)
    {
        $options = self::getPluginOptions($package);
        // specific name
        if ($options['anime-db-bundle']) {
            return $options['anime-db-bundle'];
        }

        $bundle = str_replace(['-', '/'], [' ', '/ '], $package->getName());
        $bundle = ucwords(strtolower($bundle));
        // TODO rename vendor AnimeDB to AnimeDb #53
        // TODO rename package anime-db/worldart-filler-bundle to anime-db/world-art-filler-bundle #5
        $bundle = str_replace([' ', 'Db', 'art'], ['', 'DB', 'Art'], $bundle);
        $bundle = explode('/', $bundle);
        return '\\'.$bundle[0].'\Bundle\\'.$bundle[1].'\\'.$bundle[0].$bundle[1];
    }

    /**
     * Get bundle root dir from package
     *
     * @param \Composer\Package\PackageInterface $package
     *
     * @return string|boolean
     */
    protected static function getBundleRootDir(PackageInterface $package)
    {
        $bundle = self::getBundleClass($package);

        $loader = require __DIR__.'/../../../../../vendor/autoload.php';
        if ($file = $loader->findFile($bundle)) {
            return pathinfo($file, PATHINFO_DIRNAME);
        } else {
            return false;
        }
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
        $options = array_merge(['symfony-app-dir' => 'app'], $event->getComposer()->getPackage()->getExtra());

        $php = escapeshellarg(self::getPhp());
        $console = escapeshellarg($options['symfony-app-dir'].'/console');
        if ($event->getIO()->isDecorated()) {
            $console .= ' --ansi';
        }

        $process = new Process($php.' '.$console.' '.$cmd, null, null, null, $timeout);
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });
        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf('An error occurred when executing the "%s" command.', escapeshellarg($cmd)));
        }
    }

    /**
     * Get plugin options
     *
     * @param \Composer\Package\PackageInterface $package
     *
     * @return array
     */
    protected static function getPluginOptions(PackageInterface $package)
    {
        return array_merge(array(
            'anime-db-bundle' => '',
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
}