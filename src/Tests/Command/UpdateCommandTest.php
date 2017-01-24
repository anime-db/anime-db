<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Command;

use AnimeDb\Bundle\AnimeDbBundle\Tests\TestCaseWritable;
use AnimeDb\Bundle\AnimeDbBundle\Command\UpdateCommand;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Composer;
use AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\StoreEvents;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Composer\Package\RootPackageInterface;
use Symfony\Component\Filesystem\Filesystem;
use AnimeDb\Bundle\AnimeDbBundle\Client\GitHub;
use Composer\Package\Package;
use AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded;
use AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Updated;

class UpdateCommandTest extends TestCaseWritable
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|InputInterface
     */
    protected $input;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|OutputInterface
     */
    protected $output;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Composer
     */
    protected $composer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|GitHub
     */
    protected $github;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface
     */
    protected $container;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RootPackageInterface
     */
    protected $package;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Filesystem
     */
    protected $filesystem;

    /**
     * Target dir.
     *
     * @var string
     */
    protected $target;

    protected function setUp()
    {
        parent::setUp();
        $this->input = $this->getMock('\Symfony\Component\Console\Input\InputInterface');
        $this->output = $this->getMock('\Symfony\Component\Console\Output\OutputInterface');
        $this->container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
        $this->package = $this->getMock('\Composer\Package\RootPackageInterface');
        $this->filesystem = $this->getMock('\Symfony\Component\Filesystem\Filesystem');
        $this->composer = $this
            ->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Composer\Composer')
            ->disableOriginalConstructor()
            ->getMock();
        $this->github = $this
            ->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Client\GitHub')
            ->disableOriginalConstructor()
            ->getMock();
        $this->target = sys_get_temp_dir().'/anime-db';
    }

    public function testConfigure()
    {
        $command = new UpdateCommand();
        $this->assertEquals('animedb:update', $command->getName());
        $this->assertNotEmpty($command->getDescription());
    }

    /**
     * @return array
     */
    public function getExecuteResult()
    {
        return [
            [
                0,
                '<info>Update requirements has been completed</info>',
            ],
            [
                1,
                '<error>During updating dependencies error occurred</error>',
            ],
        ];
    }

    /**
     * @dataProvider getExecuteResult
     *
     * @param int $result
     * @param string $message
     */
    public function testExecute($result, $message)
    {
        $command = $this->getCommandToExecute(['name' => '1.0.0'], '1.0.0', $result);
        $this->write([
            '<info>Application has already been updated to the latest version</info>',
            $message,
            '<info>Updating the application has been completed<info>',
        ]);

        $command->run($this->input, $this->output); // test
    }

    public function testExecuteUpdateItself()
    {
        $tag = [
            'name' => '1.1.0-alpha',
            'zipball_url' => 'http://example.com/tags/1.0.1.zip',
        ];
        $dispatcher = $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $local_dispatcher = $this
            ->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Event\Dispatcher')
            ->disableOriginalConstructor()
            ->getMock();
        $package = $this->getMock('\Composer\Package\RootPackageInterface');

        // vars for closures
        $that = $this;
        $fs = $this->fs;
        $root_dir = $this->root_dir;
        $target = $this->target;
        $root_package = $this->package;

        // create files for Finder
        $this->fs->mkdir([
            $this->root_dir.'app/cache',
            $this->root_dir.'app/DoctrineMigrations/',
            $this->root_dir.'app/Resources/views/',
            $this->root_dir.'app/config/',
            $this->root_dir.'src/Tests/',
            $this->root_dir.'src/Console',
        ]);
        $this->fs->touch([
            $this->root_dir.'app/bootstrap.php.cache',
            $this->root_dir.'app/DoctrineMigrations/Version11111111111111_Demo.php',
            $this->root_dir.'app/Resources/views/base.html.twig',
            $this->root_dir.'app/config/config.yml',
            $this->root_dir.'src/AnimeDbAnimeDbBundle.php',
            $this->root_dir.'src/Tests/TestCaseWritable.php',
        ]);

        // init command
        $command = $this->getCommandToExecute($tag, '1.0.0', 0);

        $this->write([
            'Discovered a new version of the application: <info>'.$tag['name'].'</info>',
            '<info>Update itself has been completed</info>',
            '<info>Update requirements has been completed</info>',
            '<info>Updating the application has been completed<info>',
        ]);
        $this->container
            ->expects($this->at(2))
            ->method('get')
            ->will($this->returnValue($this->filesystem))
            ->with('filesystem');
        $this->container
            ->expects($this->at(3))
            ->method('get')
            ->will($this->returnValue($dispatcher))
            ->with('event_dispatcher');
        $this->container
            ->expects($this->at(4))
            ->method('get')
            ->will($this->returnValue($this->filesystem))
            ->with('filesystem');
        $this->container
            ->expects($this->at(8))
            ->method('get')
            ->will($this->returnValue($local_dispatcher))
            ->with('anime_db.event_dispatcher');
        $this->container
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->will($this->returnValue($this->root_dir.'app'))
            ->with('kernel.root_dir');
        $this->composer
            ->expects($this->once())
            ->method('download')
            ->will($this->returnCallback(function ($package, $_target) use ($that, $target, $tag) {
                $that->assertEquals($target, $_target);
                // check package
                /* @var $package Package */
                $that->assertInstanceOf('\Composer\Package\Package', $package);
                $that->assertEquals('anime-db/anime-db', $package->getName());
                $that->assertEquals(Composer::getVersionCompatible($tag['name']), $package->getVersion());
                $that->assertEquals($tag['name'], $package->getPrettyVersion());
                $that->assertEquals('zip', $package->getDistType());
                $that->assertEquals($tag['zipball_url'], $package->getDistUrl());
                $that->assertEquals('dist', $package->getInstallationSource());
            }));
        $this->composer
            ->expects($this->once())
            ->method('getPackageFromConfigFile')
            ->with($this->target.'/composer.json')
            ->will($this->returnValue($package));
        $this->composer
            ->expects($this->once())
            ->method('reload');
        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->will($this->returnCallback(function ($name, $event) use ($that, $target, $package, $root_package) {
                $that->assertEquals(StoreEvents::DOWNLOADED, $name);
                // check event
                /* @var $event Downloaded */
                $that->assertInstanceOf('\AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded', $event);
                $that->assertEquals($target, $event->getPath());
                $that->assertEquals($package, $event->getNewPackage());
                $that->assertEquals($root_package, $event->getOldPackage());
            }));
        $local_dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->will($this->returnCallback(function ($name, $event) use ($that, $package) {
                $that->assertEquals(StoreEvents::UPDATED, $name);
                /* @var $event Updated */
                $that->assertInstanceOf('\AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Updated', $event);
                $that->assertEquals($package, $event->getPackage());
            }));
        $this->filesystem
            ->expects($this->at(0))
            ->method('remove')
            ->with($this->target);
        $this->filesystem
            ->expects($this->at(1))
            ->method('remove')
            ->will($this->returnCallback(function ($finder) use ($that, $fs, $root_dir) {
                $that->assertInstanceOf('\Symfony\Component\Finder\Finder', $finder);
                // test remove files from Finder
                $fs->remove($finder);
                $that->assertFileExists($root_dir.'app/bootstrap.php.cache');
                $that->assertFileExists($root_dir.'app/DoctrineMigrations/Version11111111111111_Demo.php');
                $that->assertFileExists($root_dir.'app/Resources/views/base.html.twig');
                $that->assertFileExists($root_dir.'app/config');
                $that->assertFileExists($root_dir.'src/Console');
                $that->assertFileNotExists($root_dir.'app/config/config.yml');
                $that->assertFileNotExists($root_dir.'src/AnimeDbAnimeDbBundle.php');
                $that->assertFileNotExists($root_dir.'src/Tests/TestCaseWritable.php');
            }));
        $this->filesystem
            ->expects($this->at(3))
            ->method('remove')
            ->with($this->target);
        $this->filesystem
            ->expects($this->once())
            ->method('mirror')
            ->with($this->target, $this->root_dir.'app/../', null, ['override' => true, 'copy_on_windows' => true]);

        $command->run($this->input, $this->output); // test
    }

    /**
     * @param array $tag
     * @param string $current_version
     * @param int $result
     *
     * @return UpdateCommand
     */
    protected function getCommandToExecute(array $tag, $current_version, $result)
    {
        $that = $this;
        $installer = $this
            ->getMockBuilder('\Composer\Installer')
            ->disableOriginalConstructor()
            ->getMock();
        $installer
            ->expects($this->once())
            ->method('run')
            ->will($this->returnValue($result));
        $this->container
            ->expects($that->at(0))
            ->method('get')
            ->will($this->returnValue($this->composer))
            ->with('anime_db.composer');
        $this->container
            ->expects($that->at(1))
            ->method('get')
            ->will($this->returnValue($this->github))
            ->with('anime_db.client.github');
        $this->composer
            ->expects($this->once())
            ->method('setIO')
            ->will($this->returnCallback(function ($io) use ($that) {
                $that->assertInstanceOf('\Composer\IO\ConsoleIO', $io);
            }));
        $this->github
            ->expects($this->once())
            ->method('getLastRelease')
            ->will($this->returnValue($tag))
            ->with('anime-db/anime-db');
        $this->composer
            ->expects($this->atLeastOnce())
            ->method('getRootPackage')
            ->will($this->returnValue($this->package));
        $this->composer
            ->expects($this->once())
            ->method('getInstaller')
            ->will($this->returnValue($installer));
        $this->package
            ->expects($this->once())
            ->method('getPrettyVersion')
            ->will($this->returnValue($current_version));
        /* @var $helper_set HelperSet */
        $helper_set = $this
            ->getMockBuilder('\Symfony\Component\Console\Helper\HelperSet')
            ->disableOriginalConstructor()
            ->getMock();
        $this->output
            ->expects($this->at(0))
            ->method('writeln')
            ->with('Search for a new version of the application');

        $command = new UpdateCommand();
        $command->setHelperSet($helper_set);
        $command->setContainer($this->container);

        return $command;
    }

    /**
     * Write messages.
     *
     * @param string[] $messages
     */
    protected function write(array $messages)
    {
        foreach ($messages as $key => $message) {
            $this->output
                ->expects($this->at($key + 1))
                ->method('writeln')
                ->with($message);
        }
    }
}
