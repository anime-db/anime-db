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
use Composer\Package\RootPackageInterface;
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
use Symfony\Component\Filesystem\Filesystem;

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
     * Root dir
     *
     * @var string
     */
    private static $root_dir;

    /**
     * Get container of jobs
     *
     * @return \AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container
     */
    public static function getContainer()
    {
        if (!(self::$container instanceof Container)) {
            self::setContainer(new Container());
        }
        return self::$container;
    }

    /**
     * Set container of jobs
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container $container
     */
    public static function setContainer(Container $container)
    {
        self::$container = $container;
    }

    /**
     * Get root dir
     *
     * @return string
     */
    public static function getRootDir()
    {
        if (!self::$root_dir) {
            self::setRootDir(__DIR__.'/../../app/');
        }
        return self::$root_dir;
    }

    /**
     * Set root dir
     *
     * @param string $root_dir
     */
    public static function setRootDir($root_dir)
    {
        self::$root_dir = $root_dir;
    }

    /**
     * Add or remove package in kernel
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function packageInKernel(PackageEvent $event)
    {
        switch ($event->getOperation()->getJobType()) {
            case 'install':
                self::getContainer()->addJob(new AddKernel($event->getOperation()->getPackage()));
                break;
            case 'update':
                self::getContainer()->addJob(new AddKernel($event->getOperation()->getTargetPackage()));
                break;
            case 'uninstall':
                self::getContainer()->addJob(new RemoveKernel($event->getOperation()->getPackage()));
                break;
        }
    }

    /**
     * Add or remove packages in routing
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function packageInRouting(PackageEvent $event)
    {
        switch ($event->getOperation()->getJobType()) {
            case 'install':
                self::getContainer()->addJob(new AddRouting($event->getOperation()->getPackage()));
                break;
            case 'update':
                self::getContainer()->addJob(new AddRouting($event->getOperation()->getTargetPackage()));
                break;
            case 'uninstall':
                self::getContainer()->addJob(new RemoveRouting($event->getOperation()->getPackage()));
                break;
        }
    }

    /**
     * Add or remove packages in config
     *
     * @param \Composer\Script\PackageEvent $event
     */
    public static function packageInConfig(PackageEvent $event)
    {
        switch ($event->getOperation()->getJobType()) {
            case 'install':
                self::getContainer()->addJob(new AddConfig($event->getOperation()->getPackage()));
                break;
            case 'update':
                self::getContainer()->addJob(new AddConfig($event->getOperation()->getTargetPackage()));
                break;
            case 'uninstall':
                self::getContainer()->addJob(new RemoveConfig($event->getOperation()->getPackage()));
                break;
        }
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
                self::getContainer()->addJob(new UpMigrate($event->getOperation()->getPackage()));
                break;
            case 'update':
                self::getContainer()->addJob(new UpMigrate($event->getOperation()->getTargetPackage()));
                break;
            case 'uninstall':
                self::getContainer()->addJob(new DownMigrate($event->getOperation()->getPackage()));
                break;
        }
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
                self::getContainer()->addJob(new InstalledPackageNotify($event->getOperation()->getPackage()));
                break;
            case 'update':
                self::getContainer()->addJob(new UpdatedPackageNotify($event->getOperation()->getTargetPackage()));
                break;
            case 'uninstall':
                self::getContainer()->addJob(new RemovedPackageNotify($event->getOperation()->getPackage()));
                break;
        }
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
     */
    public static function execJobs()
    {
        self::getContainer()->execute();
    }

    /**
     * Install config files
     */
    public static function installConfig()
    {
        if (!file_exists(self::getRootDir().'config/vendor_config.yml')) {
            file_put_contents(self::getRootDir().'config/vendor_config.yml', '');
        }
        if (!file_exists(self::getRootDir().'config/routing.yml')) {
            file_put_contents(self::getRootDir().'config/routing.yml', '');
        }
        if (!file_exists(self::getRootDir().'bundles.php')) {
            file_put_contents(self::getRootDir().'bundles.php', "<?php\nreturn [\n];");
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
        $dir = self::getRootDir().'DoctrineMigrations';
        if (self::isHaveMigrations($dir)) {
            self::repackMigrations($dir);

            $cmd = 'doctrine:migrations:migrate --no-interaction';
            if ($event->getIO()->isDecorated()) {
                $cmd .= ' --ansi';
            }
            self::getContainer()->executeCommand($cmd, null);
        }
    }

    /**
     * Migrate all plugins to down
     */
    public static function migrateDown()
    {
        $dir = self::getRootDir().'cache/dev/DoctrineMigrations/';
        if (self::isHaveMigrations($dir)) {
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
     * Is have migrations
     *
     * @param string $dir
     *
     * @return boolean
     */
    protected static function isHaveMigrations($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }

        return (bool)Finder::create()
            ->in($dir)
            ->files()
            ->name('/Version\d{14}.*\.php/')
            ->count();
    }

    /**
     * Repack migrations
     *
     * @param string $dir
     */
    protected static function repackMigrations($dir)
    {
        if (is_dir($dir)) {
            $finder = Finder::create()
                ->in($dir)
                ->files()
                ->name('/Version\d{14}.*\.php/');

            foreach ($finder as $file) {
                /* @var $file \SplFileInfo */
                $content = file_get_contents($file);
                if (strpos($content, 'getMigrationClass()') !== false) {
                    $content = str_replace('getMigrationClass()', 'getMigration()', $content);
                    $content = preg_replace('/return "([^"]+)";/', 'return new \\\$1($this->version);', $content);
                    file_put_contents($file, $content);
                }
            }
        }
    }

    /**
     * Сreate a backup of the database
     */
    public static function backupDB() {
        $db = self::getRootDir().'Resources/anime.db';
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

    /**
     * Clears the Symfony cache
     */
    public static function clearCache()
    {
        self::getContainer()->executeCommand('cache:clear --no-warmup --env=prod --no-debug', 0);
        self::getContainer()->executeCommand('cache:clear --no-warmup --env=test --no-debug', 0);
        self::getContainer()->executeCommand('cache:clear --no-warmup --env=dev --no-debug', 0);
    }
}