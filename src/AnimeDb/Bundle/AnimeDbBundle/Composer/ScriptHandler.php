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
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Installed as InstalledNotify;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Removed as RemovedNotify;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Updated as UpdatedNotify;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Down as DownMigrate;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Up as UpMigrate;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config\Add as AddConfig;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config\Remove as RemoveConfig;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Kernel\Add as AddKernel;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Kernel\Remove as RemoveKernel;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing\Add as AddRouting;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing\Remove as RemoveRouting;

/**
 * Composer script handler
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ScriptHandler
{
    /**
     * Container of jobs
     *
     * @var \AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container|null
     */
    private static $container;

    /**
     * Get container of jobs
     *
     * @return\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container
     */
    protected static function getContainer()
    {
        if (!(self::$container instanceof Container)) {
            self::$container = new Container();
        }
        return self::$container;
    }

    /**
     * Add package to AppKernel
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function addPackageToAppKernel(PackageEvent $event)
    {
        self::getContainer()->addJob(new AddKernel($event->getOperation()->getPackage()));
    }

    /**
     * Remove packages from AppKernel
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function removePackageFromAppKernel(PackageEvent $event)
    {
        self::getContainer()->addJob(new RemoveKernel($event->getOperation()->getPackage()));
    }

    /**
     * Add packages to routing
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function addPackageToRouting(PackageEvent $event)
    {
        self::getContainer()->addJob(new AddRouting($event->getOperation()->getPackage()));
    }

    /**
     * Remove packages from routing
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function removePackageFromRouting(PackageEvent $event)
    {
        self::getContainer()->addJob(new RemoveRouting($event->getOperation()->getPackage()));
    }

    /**
     * Add packages to config
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function addPackageToConfig(PackageEvent $event)
    {
        self::getContainer()->addJob(new AddConfig($event->getOperation()->getPackage()));
    }

    /**
     * Remove packages from config
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function removePackageFromConfig(PackageEvent $event)
    {
        self::getContainer()->addJob(new RemoveConfig($event->getOperation()->getPackage()));
    }

    /**
     * Migrate packages
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function migratePackage(PackageEvent $event)
    {
        switch ($event->getOperation()->getJobType()) {
            case 'install':
                $job = new UpMigrate($event->getOperation()->getPackage());
                break;
            case 'update':
                $job = new UpMigrate($event->getOperation()->getTargetPackage());
                break;
            case 'uninstall':
                // migrate down before uninstall
                $job = new DownMigrate($event->getOperation()->getPackage());
                $job->setContainer(self::getContainer());
                $job->execute();
            default:
                return;
        }
        self::getContainer()->addJob($job);
    }

    /**
     * Notify listeners that the package has been installed/updated/removed
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function notifyPackage(PackageEvent $event)
    {
        switch ($event->getOperation()->getJobType()) {
            case 'install':
                $job = new InstalledNotify($event->getOperation()->getPackage());
                break;
            case 'update':
                $job = new UpdatedNotify($event->getOperation()->getTargetPackage());
                break;
            case 'uninstall':
                $job = new RemovedNotify($event->getOperation()->getPackage());
                break;
            default:
                return;
        }
        self::getContainer()->addJob($job);
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
            'anime-db-config' => '',
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
    public static function getPhp()
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
     * Execution pending jobs
     *
     * @param \Composer\Script\CommandEvent $event
     */
    public static function execJobs(CommandEvent $event)
    {
        self::getContainer()->execute();
    }

    /**
     * Install config files
     *
     * @param \Composer\Script\CommandEvent $event
     */
    public static function installConfig(CommandEvent $event)
    {
        if (!file_exists(__DIR__.'/../../../../../app/config/bundle_config.yml')) {
            file_put_contents(__DIR__.'/../../../../../app/config/bundle_config.yml', '');
        }
        if (!file_exists(__DIR__.'/../../../../../app/config/routing.yml')) {
            file_put_contents(__DIR__.'/../../../../../app/config/routing.yml', '');
        }
        if (!file_exists(__DIR__.'/../../../../../app/bundles.php')) {
            file_put_contents(__DIR__.'/../../../../../app/bundles.php', "<?php\nreturn [\n];");
        }
    }
}