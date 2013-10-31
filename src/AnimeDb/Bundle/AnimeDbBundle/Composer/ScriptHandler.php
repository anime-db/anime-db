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
     * Add package to kernel
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function addPackageToKernel(PackageEvent $event)
    {
        self::getContainer()->addJob(new AddKernel($event->getOperation()->getPackage()));
    }

    /**
     * Remove packages from kernel
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function removePackageFromKernel(PackageEvent $event)
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
     * Global migrate
     *
     * @param \Composer\Script\CommandEvent $event
     */
    public static function migrate(CommandEvent $event)
    {
        $cmd = 'doctrine:migrations:migrate --no-interaction';
        if ($event->getIO()->isDecorated()) {
            $cmd .= ' --ansi';
        }
        self::getContainer()->executeCommand($cmd, null);
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