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

use Composer\Installer\PackageEvent;
use Composer\Script\Event;
use Composer\IO\IOInterface;
use Composer\DependencyResolver\Operation\OperationInterface;
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
     * @var Container|null
     */
    private static $container;

    /**
     * @var string
     */
    private static $root_dir;

    /**
     * @return Container
     */
    public static function getContainer()
    {
        if (!(self::$container instanceof Container)) {
            self::setContainer(new Container(self::getRootDir()));
        }

        return self::$container;
    }

    /**
     * @param Container $container
     */
    public static function setContainer(Container $container)
    {
        self::$container = $container;
    }

    /**
     * @return string
     */
    public static function getRootDir()
    {
        if (!self::$root_dir) {
            self::setRootDir(getcwd().'/app/');
        }

        return self::$root_dir;
    }

    /**
     * @param string $root_dir
     */
    public static function setRootDir($root_dir)
    {
        self::$root_dir = $root_dir;
    }

    /**
     * Add or remove package in kernel
     *
     * @param PackageEvent $event
     */
    public static function packageInKernel(PackageEvent $event)
    {
        self::addJobByOperationType(
            $event->getOperation(),
            function ($package) {
                return new AddKernel($package);
            },
            function ($package) {
                return new RemoveKernel($package);
            }
        );
    }

    /**
     * Add or remove packages in routing
     *
     * @param PackageEvent $event
     */
    public static function packageInRouting(PackageEvent $event)
    {
        self::addJobByOperationType(
            $event->getOperation(),
            function ($package) {
                return new AddRouting($package);
            },
            function ($package) {
                return new RemoveRouting($package);
            }
        );
    }

    /**
     * Add or remove packages in config
     *
     * @param PackageEvent $event
     */
    public static function packageInConfig(PackageEvent $event)
    {
        self::addJobByOperationType(
            $event->getOperation(),
            function ($package) {
                return new AddConfig($package);
            },
            function ($package) {
                return new RemoveConfig($package);
            }
        );
    }

    /**
     * Migrate packages
     *
     * @param PackageEvent $event
     */
    public static function migratePackage(PackageEvent $event)
    {
        self::addJobByOperationType(
            $event->getOperation(),
            function ($package) {
                return new UpMigrate($package);
            },
            function ($package) {
                return new DownMigrate($package);
            }
        );
    }

    /**
     * Notify listeners that the package has been installed/updated/removed
     *
     * @param PackageEvent $event
     */
    public static function notifyPackage(PackageEvent $event)
    {
        self::addJobByOperationType(
            $event->getOperation(),
            function ($package) {
                return new InstalledPackageNotify($package);
            },
            function ($package) {
                return new RemovedPackageNotify($package);
            },
            function ($package) {
                return new UpdatedPackageNotify($package);
            }
        );
    }

    /**
     * Add job by operation type
     *
     * @param OperationInterface $operation
     * @param \Closure $install
     * @param \Closure $update
     * @param \Closure $uninstall
     */
    protected static function addJobByOperationType(
        OperationInterface $operation,
        \Closure $install,
        \Closure $uninstall,
        \Closure $update = null
    ) {
        switch ($operation->getJobType()) {
            case 'install':
                self::getContainer()->addJob($install($operation->getPackage()));
                break;
            case 'uninstall':
                self::getContainer()->addJob($uninstall($operation->getPackage()));
                break;
            case 'update':
                $update = $update ?: $install;
                self::getContainer()->addJob($update($operation->getTargetPackage()));
                break;
        }
    }

    /**
     * Notify listeners that the project has been installed
     *
     * @param Event $event
     */
    public static function notifyProjectInstall(Event $event)
    {
        self::getContainer()->addJob(new InstalledProjectNotify($event->getComposer()->getPackage()));
    }

    /**
     * Notify listeners that the project has been updated
     *
     * @param Event $event
     */
    public static function notifyProjectUpdate(Event $event)
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
     * @param Event $event
     */
    public static function deliverEvents(Event $event)
    {
        self::executeCommand('animedb:deliver-events', $event->getIO());
    }

    /**
     * Migrate all plugins to up
     *
     * @param Event $event
     */
    public static function migrateUp(Event $event)
    {
        $dir = self::getRootDir().'DoctrineMigrations';
        if (self::isHaveMigrations($dir)) {
            self::repackMigrations($dir);
            self::executeCommand('doctrine:migrations:migrate --no-interaction', $event->getIO());
        }
    }

    /**
     * Migrate all plugins to down
     *
     * @param Event $event
     */
    public static function migrateDown(Event $event)
    {
        $dir = self::getRootDir().'cache/dev/DoctrineMigrations/';
        if (self::isHaveMigrations($dir)) {
            file_put_contents(
                $dir.'migrations.yml',
                "migrations_namespace: 'Application\Migrations'\n".
                "migrations_directory: 'app/cache/dev/DoctrineMigrations/'\n".
                "table_name: 'migration_versions'"
            );

            self::executeCommand(
                'doctrine:migrations:migrate --no-interaction --configuration='.$dir.'migrations.yml 0',
                $event->getIO()
            );
        }
    }

    /**
     * @param string $dir
     *
     * @return bool
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
     * @param \Composer\Script\Event $event
     */
    public static function dumpAssets(Event $event)
    {
        self::executeCommand('assetic:dump --env=prod --no-debug --force web', $event->getIO());
    }

    /**
     * Clears the Symfony cache
     *
     * @param \Composer\Script\Event $event
     */
    public static function clearCache(Event $event)
    {
        // to avoid errors due to the encrypted container forcibly clean the cache directory
        $dir = self::getRootDir().'cache/prod';
        if (is_dir($dir)) {
            (new Filesystem())->remove($dir);
        }

        self::executeCommand('cache:clear --no-warmup --env=prod --no-debug', $event->getIO());
        self::executeCommand('cache:clear --no-warmup --env=dev --no-debug', $event->getIO());
    }

    /**
     * @param string $cmd
     * @param IOInterface $io
     */
    protected static function executeCommand($cmd, IOInterface $io)
    {
        if ($io->isDecorated()) {
            $cmd .= ' --ansi';
        }
        self::getContainer()->executeCommand($cmd, 0);
    }
}
