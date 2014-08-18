<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Composer;

use AnimeDb\Bundle\AnimeDbBundle\Composer\ScriptHandler;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Test script handler
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Composer
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ScriptHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Container
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $container;

    /**
     * Default container
     *
     * @var \AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container
     */
    protected $default_container;

    /**
     * Command event
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $event_command;

    /**
     * Package event
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $event_package;

    /**
     * Composer
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $composer;

    /**
     * Package
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $package;

    /**
     * Default root dir
     *
     * @var string
     */
    protected $default_root_dir;

    /**
     * Root dir
     *
     * @var string
     */
    protected $root_dir;

    /**
     * Filesystem
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * Construct
     *
     * @param string $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->fs = new Filesystem();
    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $this->root_dir = sys_get_temp_dir().'/tests/';
        $this->fs->mkdir($this->root_dir);

        $this->composer = $this->getMock('\Composer\Composer');
        $this->package = $this->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        // this method is called in the job that we did not test
        $this->package
            ->expects($this->any())
            ->method('getExtra')
            ->willReturn([]);
        $this->event_command = $this->getMockBuilder('\Composer\Script\CommandEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $this->event_package = $this->getMockBuilder('\Composer\Script\PackageEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $this->container = $this->getMock('\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container');

        $this->default_container = ScriptHandler::getContainer();
        ScriptHandler::setContainer($this->container);
        $this->default_root_dir = ScriptHandler::getRootDir();
        ScriptHandler::setRootDir($this->root_dir);

    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        parent::tearDown();
        ScriptHandler::setContainer($this->default_container);
        ScriptHandler::setRootDir($this->default_root_dir);
        $this->fs->remove($this->root_dir);
    }

    /**
     * Test get container lazy load
     */
    public function testGetContainerLazyLoad()
    {
        $this->assertEquals($this->container, ScriptHandler::getContainer());
    }

    /**
     * Get data for registr package
     *
     * @return array
     */
    public function getDataFroRegistrPackage()
    {
        return [
            // packageInKernel
            [
                'install',
                'getPackage',
                'packageInKernel',
                '\Composer\DependencyResolver\Operation\InstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Kernel\Add'
            ],
            [
                'update',
                'getTargetPackage',
                'packageInKernel',
                '\Composer\DependencyResolver\Operation\UpdateOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Kernel\Add'
            ],
            [
                'uninstall',
                'getPackage',
                'packageInKernel',
                '\Composer\DependencyResolver\Operation\UninstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Kernel\Remove'
            ],
            // packageInRouting
            [
                'install',
                'getPackage',
                'packageInRouting',
                '\Composer\DependencyResolver\Operation\InstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing\Add'
            ],
            [
                'update',
                'getTargetPackage',
                'packageInRouting',
                '\Composer\DependencyResolver\Operation\UpdateOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing\Add'
            ],
            [
                'uninstall',
                'getPackage',
                'packageInRouting',
                '\Composer\DependencyResolver\Operation\UninstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing\Remove'
            ],
            // packageInConfig
            [
                'install',
                'getPackage',
                'packageInConfig',
                '\Composer\DependencyResolver\Operation\InstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config\Add'
            ],
            [
                'update',
                'getTargetPackage',
                'packageInConfig',
                '\Composer\DependencyResolver\Operation\UpdateOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config\Add'
            ],
            [
                'uninstall',
                'getPackage',
                'packageInConfig',
                '\Composer\DependencyResolver\Operation\UninstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config\Remove'
            ],
            // migratePackage
            [
                'install',
                'getPackage',
                'migratePackage',
                '\Composer\DependencyResolver\Operation\InstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Up'
            ],
            [
                'update',
                'getTargetPackage',
                'migratePackage',
                '\Composer\DependencyResolver\Operation\UpdateOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Up'
            ],
            [
                'uninstall',
                'getPackage',
                'migratePackage',
                '\Composer\DependencyResolver\Operation\UninstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Down'
            ],
            // notifyPackage
            [
                'install',
                'getPackage',
                'notifyPackage',
                '\Composer\DependencyResolver\Operation\InstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Package\Installed'
            ],
            [
                'update',
                'getTargetPackage',
                'notifyPackage',
                '\Composer\DependencyResolver\Operation\UpdateOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Package\Updated'
            ],
            [
                'uninstall',
                'getPackage',
                'notifyPackage',
                '\Composer\DependencyResolver\Operation\UninstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Package\Removed'
            ],
        ];
    }

    /**
     * Test registr package
     *
     * @dataProvider getDataFroRegistrPackage
     *
     * @param string $type
     * @param string $method
     * @param string $test
     * @param string $operation_class
     * @param string $job_class
     */
    public function testRegistrPackage($type, $method, $test, $operation_class, $job_class)
    {
        $operation = $this->getMockBuilder($operation_class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->event_package
            ->expects($this->atLeastOnce())
            ->method('getOperation')
            ->willReturn($operation);
        $operation
            ->expects($this->once())
            ->method('getJobType')
            ->willReturn($type);
        $operation
            ->expects($this->once())
            ->method($method)
            ->willReturn($this->package);
        $that = $this;
        $package = $this->package;
        $this->container
            ->expects($this->once())
            ->method('addJob')
            ->willReturnCallback(function ($job) use ($that, $package, $job_class) {
                $that->assertInstanceOf($job_class, $job);
                $that->assertEquals($package, $job->getPackage());
            });

        call_user_func(['\AnimeDb\Bundle\AnimeDbBundle\Composer\ScriptHandler', $test], $this->event_package);
    }

    /**
     * Get data from registr package undefined type
     *
     * @return array
     */
    public function getDataFromRegistrPackageUndefinedType()
    {
        return [
            ['packageInKernel'],
            ['packageInRouting'],
            ['packageInConfig'],
            ['migratePackage'],
            ['notifyPackage'],
        ];
    }

    /**
     * Test registr package undefined job type
     *
     * @dataProvider getDataFromRegistrPackageUndefinedType
     *
     * @param string $method
     */
    public function testRegistrPackageUndefinedType($method)
    {
        $operation = $this->getMock('\Composer\DependencyResolver\Operation\OperationInterface');
        $this->event_package
            ->expects($this->atLeastOnce())
            ->method('getOperation')
            ->willReturn($operation);
        $operation
            ->expects($this->once())
            ->method('getJobType')
            ->willReturn('undefined');
        $operation
            ->expects($this->never())
            ->method('getPackage');
        $operation
            ->expects($this->never())
            ->method('getTargetPackage');

        call_user_func(['\AnimeDb\Bundle\AnimeDbBundle\Composer\ScriptHandler', $method], $this->event_package);
    }

    /**
     * Get job for notify project
     *
     * @return array
     */
    public function getJobForNotifyProject()
    {
        return [
            [
                'notifyProjectInstall',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Project\Installed'
            ],
            [
                'notifyProjectUpdate',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Project\Updated'
            ],
        ];
    }

    /**
     * Test notify project
     *
     * @dataProvider getJobForNotifyProject
     *
     * @param string $method
     * @param string $job_class
     */
    public function testNotifyProject($method, $job_class)
    {
        $this->getRootPackage();
        $that = $this;
        $package = $this->package;
        $this->container
            ->expects($this->once())
            ->method('addJob')
            ->willReturnCallback(function ($job) use ($that, $package, $job_class) {
                $that->assertInstanceOf($job_class, $job);
                $that->assertEquals($package, $job->getPackage());
            });
        call_user_func(['\AnimeDb\Bundle\AnimeDbBundle\Composer\ScriptHandler', $method], $this->event_command);
    }

    /**
     * Get root package for job
     */
    protected function getRootPackage()
    {
        $this->event_command
            ->expects($this->once())
            ->method('getComposer')
            ->willReturn($this->composer);
        $this->composer
            ->expects($this->once())
            ->method('getPackage')
            ->willReturn($this->package);
    }

    /**
     * Test exec jobs
     */
    public function testExecJobs()
    {
        $this->container
            ->expects($this->once())
            ->method('execute');
        ScriptHandler::execJobs();
    }

    /**
     * Test install config not exists
     */
    public function testInstallConfigNotExists()
    {
        $this->fs->mkdir($this->root_dir.'config');

        ScriptHandler::installConfig(); // test

        $this->assertFileExists($this->root_dir.'config/vendor_config.yml');
        $this->assertFileExists($this->root_dir.'config/routing.yml');
        $this->assertFileExists($this->root_dir.'bundles.php');
        $this->assertEmpty(file_get_contents($this->root_dir.'config/vendor_config.yml'));
        $this->assertEmpty(file_get_contents($this->root_dir.'config/routing.yml'));
        $this->assertEquals("<?php\nreturn [\n];", file_get_contents($this->root_dir.'bundles.php'));
    }

    /**
     * Test install config
     */
    public function testInstallConfig()
    {
        $this->fs->mkdir($this->root_dir.'config');
        touch($this->root_dir.'config/vendor_config.yml');
        touch($this->root_dir.'config/routing.yml');
        touch($this->root_dir.'bundles.php');

        ScriptHandler::installConfig(); // test

        $this->assertFileExists($this->root_dir.'config/vendor_config.yml');
        $this->assertFileExists($this->root_dir.'config/routing.yml');
        $this->assertFileExists($this->root_dir.'bundles.php');
        $this->assertEmpty(file_get_contents($this->root_dir.'config/vendor_config.yml'));
        $this->assertEmpty(file_get_contents($this->root_dir.'config/routing.yml'));
        $this->assertEmpty(file_get_contents($this->root_dir.'bundles.php'));
    }

    /**
     * Test deliver events
     */
    public function testDeliverEvents()
    {
        $io = $this->getMock('\Composer\IO\IOInterface');
        $this->event_command
            ->expects($this->exactly(2))
            ->method('getIO')
            ->willReturn($io);
        $io
            ->expects($this->at(0))
            ->method('isDecorated')
            ->willReturn(false);
        $io
            ->expects($this->at(1))
            ->method('isDecorated')
            ->willReturn(true);
        $this->container
            ->expects($this->at(0))
            ->method('executeCommand')
            ->with('animedb:deliver-events', null);
        $this->container
            ->expects($this->at(1))
            ->method('executeCommand')
            ->with('animedb:deliver-events --ansi', null);

        ScriptHandler::deliverEvents($this->event_command);
        ScriptHandler::deliverEvents($this->event_command);
    }

    /**
     * Test migrate up no migrations
     */
    public function testMigrateUpNoMigrations()
    {
        $this->container
            ->expects($this->never())
            ->method('executeCommand');
        // dir is not exists
        ScriptHandler::migrateUp($this->event_command);

        $this->fs->mkdir($this->root_dir.'DoctrineMigrations');
        // dir is empty
        ScriptHandler::migrateDown();
    }

    /**
     * Test migrate up
     */
    public function testMigrateUp()
    {
        $dir = $this->root_dir.'DoctrineMigrations/';
        $this->fs->mkdir($dir);
        $io = $this->getMock('\Composer\IO\IOInterface');
        $this->event_command
            ->expects($this->exactly(2))
            ->method('getIO')
            ->willReturn($io);
        $io
            ->expects($this->at(0))
            ->method('isDecorated')
            ->willReturn(false);
        $io
            ->expects($this->at(1))
            ->method('isDecorated')
            ->willReturn(true);
        $this->container
            ->expects($this->at(0))
            ->method('executeCommand')
            ->with('doctrine:migrations:migrate --no-interaction', null);
        $this->container
            ->expects($this->at(1))
            ->method('executeCommand')
            ->with('doctrine:migrations:migrate --no-interaction --ansi', null);

        $this->initMigrations($dir);
        ScriptHandler::migrateUp($this->event_command); // test
        $this->checkRepackMigrations($dir);

        $this->initMigrations($dir);
        ScriptHandler::migrateUp($this->event_command); // test
        $this->checkRepackMigrations($dir);
    }

    /**
     * Init migrations
     *
     * @param string $dir
     */
    protected function initMigrations($dir)
    {
        file_put_contents(
            $dir.'Version55555555555555_DemoMigration.php',
            'public function getMigrationClass() { return "DemoMigration"; }'
        );
        file_put_contents(
            $dir.'Version66666666666666_AcmeMigration.php',
            'public function getMigration() { return new \AcmeMigration($this->version); }'
        );
    }

    /**
     * Check repack migrations
     *
     * @param string $dir
     */
    protected function checkRepackMigrations($dir)
    {
        $this->assertFileExists($dir.'Version55555555555555_DemoMigration.php');
        $this->assertFileExists($dir.'Version66666666666666_AcmeMigration.php');
        $this->assertEquals(
            'public function getMigration() { return new \DemoMigration($this->version); }',
            file_get_contents($dir.'Version55555555555555_DemoMigration.php')
        );
        $this->assertEquals(
            'public function getMigration() { return new \AcmeMigration($this->version); }',
            file_get_contents($dir.'Version66666666666666_AcmeMigration.php')
        );
    }

    /**
     * Test migrate down no migrations
     */
    public function testMigrateDownNoMigrations()
    {
        $this->container
            ->expects($this->never())
            ->method('executeCommand');
        // dir is not exists
        ScriptHandler::migrateDown();

        $this->fs->mkdir($this->root_dir.'cache/dev/DoctrineMigrations');
        // dir is empty
        ScriptHandler::migrateDown();
    }

    /**
     * Test migrate down no migrations
     */
    public function testMigrateDown()
    {
        $dir = $this->root_dir.'cache/dev/DoctrineMigrations/';
        $this->fs->mkdir($dir);
        $this->initMigrations($dir);
        $this->container
            ->expects($this->once())
            ->method('executeCommand')
            ->with('doctrine:migrations:migrate --no-interaction --configuration='.$dir.'migrations.yml 0');

        ScriptHandler::migrateDown(); // test

        $this->assertFileExists($dir.'migrations.yml');
        $this->assertEquals(
            "migrations_namespace: 'Application\Migrations'\n".
            "migrations_directory: 'app/cache/dev/DoctrineMigrations/'\n".
            "table_name: 'migration_versions'",
            file_get_contents($dir.'migrations.yml')
        );
    }

    /**
     * Test backup DB not exists
     */
    public function testBackupDBNotExists()
    {
        $this->fs->mkdir($this->root_dir.'Resources');

        ScriptHandler::backupDB(); // test

        $this->assertFileNotExists($this->root_dir.'Resources/anime.db');
        $this->assertFileNotExists($this->root_dir.'Resources/anime.db.bk');
    }

    /**
     * Test backup DB
     */
    public function testBackupDB()
    {
        $this->fs->mkdir($this->root_dir.'Resources');
        file_put_contents($this->root_dir.'Resources/anime.db', 'foo');

        ScriptHandler::backupDB(); // test

        $this->assertFileExists($this->root_dir.'Resources/anime.db');
        $this->assertFileExists($this->root_dir.'Resources/anime.db.bk');
        $this->assertFileEquals($this->root_dir.'Resources/anime.db', $this->root_dir.'Resources/anime.db.bk');
        $this->assertEquals('foo', file_get_contents($this->root_dir.'Resources/anime.db.bk'));
    }

    /**
     * Test dump assets
     */
    public function testDumpAssets()
    {
        $io = $this->getMock('\Composer\IO\IOInterface');
        $this->event_command
            ->expects($this->any())
            ->method('getIO')
            ->willReturn($io);
        $io
            ->expects($this->at(0))
            ->method('isDecorated')
            ->willReturn(false);
        $io
            ->expects($this->at(1))
            ->method('isDecorated')
            ->willReturn(true);
        $this->container
            ->expects($this->at(0))
            ->method('executeCommand')
            ->with('assetic:dump --env=prod --no-debug --force web', null);
        $this->container
            ->expects($this->at(1))
            ->method('executeCommand')
            ->with('assetic:dump --env=prod --no-debug --force --ansi web', null);

        ScriptHandler::dumpAssets($this->event_command);
        ScriptHandler::dumpAssets($this->event_command);
    }

    /**
     * Test add package to kernel
     */
    public function testAddPackageToKernel()
    {
        $this->clearCache(0, 'prod');
        $this->clearCache(1, 'test');
        $this->clearCache(2, 'dev');
        ScriptHandler::clearCache();
    }

    /**
     * Clear cache
     * @param integer $index
     * @param string $env
     */
    protected function clearCache($index, $env)
    {
        $this->container
            ->expects($this->at($index))
            ->method('executeCommand')
            ->with('cache:clear --no-warmup --env='.$env.' --no-debug', 0);
    }
}