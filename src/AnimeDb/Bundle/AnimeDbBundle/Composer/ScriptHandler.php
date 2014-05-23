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
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Package\Installed as InstalledPackageNotify;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Package\Removed as RemovedPackageNotify;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Package\Updated as UpdatedPackageNotify;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Project\Installed as InstalledProjectNotify;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Project\Updated as UpdatedProjectNotify;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Down as DownMigrate;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Up as UpMigrate;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config\Add as AddConfig;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config\Remove as RemoveConfig;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Kernel\Add as AddKernel;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Kernel\Remove as RemoveKernel;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing\Add as AddRouting;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing\Remove as RemoveRouting;
use Symfony\Component\Finder\Finder;

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
                $job = new DownMigrate($event->getOperation()->getPackage());
                break;
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
                $job = new InstalledPackageNotify($event->getOperation()->getPackage());
                break;
            case 'update':
                $job = new UpdatedPackageNotify($event->getOperation()->getTargetPackage());
                break;
            case 'uninstall':
                $job = new RemovedPackageNotify($event->getOperation()->getPackage());
                break;
            default:
                return;
        }
        self::getContainer()->addJob($job);
    }

    /**
     * Notify listeners that the project has been installed
     *
     * @param \Composer\Script\CommandEvent $event
     */
    public static function notifyProjectInstall(CommandEvent $event)
    {
        self::getContainer()->addJob(new InstalledProjectNotify($event->getComposer()->getPackage()));
    }

    /**
     * Notify listeners that the project has been updated
     *
     * @param \Composer\Script\CommandEvent $event
     */
    public static function notifyProjectUpdate(CommandEvent $event)
    {
        self::getContainer()->addJob(new UpdatedProjectNotify($event->getComposer()->getPackage()));
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
        if (!file_exists(__DIR__.'/../../../../../app/config/vendor_config.yml')) {
            file_put_contents(__DIR__.'/../../../../../app/config/vendor_config.yml', '');
        }
        if (!file_exists(__DIR__.'/../../../../../app/config/routing.yml')) {
            file_put_contents(__DIR__.'/../../../../../app/config/routing.yml', '');
        }
        if (!file_exists(__DIR__.'/../../../../../app/bundles.php')) {
            file_put_contents(__DIR__.'/../../../../../app/bundles.php', "<?php\nreturn [\n];");
        }
    }

    /**
     * Deliver deferred events
     *
     * @param \Composer\Script\CommandEvent $event
     */
    public static function deliverEvents(CommandEvent $event)
    {
        $cmd = 'animedb:deliver-events';
        if ($event->getIO()->isDecorated()) {
            $cmd .= ' --ansi';
        }
        self::getContainer()->executeCommand($cmd, null);
    }

    /**
     * Migrate all plugins to up
     *
     * @param \Composer\Script\CommandEvent $event
     */
    public static function migrateUp(CommandEvent $event)
    {
        $have_migrations = false;
        // find migrations
        if (file_exists($dir = __DIR__.'/../../../../../app/DoctrineMigrations')) {
            $have_migrations = (bool)Finder::create()
                ->in($dir)
                ->files()
                ->name('/Version\d{14}.*\.php/')
                ->count();
        }

        // migration up
        if ($have_migrations) {
            $cmd = 'doctrine:migrations:migrate --no-interaction';
            if ($event->getIO()->isDecorated()) {
                $cmd .= ' --ansi';
            }
            self::getContainer()->executeCommand($cmd, null);
        }
    }

    /**
     * Migrate all plugins to down
     *
     * @param \Composer\Script\CommandEvent $event
     */
    public static function migrateDown(CommandEvent $event)
    {
        $have_migrations = false;

        // find migrations
        if (file_exists($dir = __DIR__.'/../../../../../app/cache/dev/DoctrineMigrations/')) {
            $have_migrations = (bool)Finder::create()
                ->in($dir)
                ->files()
                ->name('/Version\d{14}.*\.php/')
                ->count();
        }

        // migration down
        if ($have_migrations) {
            file_put_contents(
                $dir.'migrations.yml',
                "migrations_namespace: 'Application\Migrations'\n".
                "migrations_directory: 'app/cache/dev/DoctrineMigrations/'\n".
                "table_name: 'migration_versions'"
            );

            self::getContainer()->executeCommand(
                'doctrine:migrations:migrate --no-interaction --configuration='.$dir.'migrations.yml 0'
            );
        }
    }

    /**
     * Сreate a backup of the database
     *
     * @param \Composer\Script\CommandEvent $event
     */
    public static function backupDB(CommandEvent $event) {
        $db = __DIR__.'/../../../../../app/Resources/anime.db';
        if (file_exists($db)) {
            copy($db, $db.'.bk');
        }
    }

    /**
     * Dumps all assets to the filesystem
     *
     * @param \Composer\Script\CommandEvent $event
     */
    public static function dumpAssets(CommandEvent $event)
    {
        $cmd = 'assetic:dump --env=prod --no-debug --force';
        if ($event->getIO()->isDecorated()) {
            $cmd .= ' --ansi';
        }
        self::getContainer()->executeCommand($cmd.' web', null);
    }
}