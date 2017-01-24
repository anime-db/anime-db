<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Composer\Job\Migrate;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Up;
use Composer\Package\Package;

class UpTest extends TestCase
{
    /**
     * @dataProvider getMigrations
     *
     * @param string $config
     */
    public function testCreateProxyMigrations($config, $file)
    {
        $file = $this->root_dir.'vendor/foo/bar/'.($config ?: $file);
        $versions = $this->root_dir.'vendor/foo/bar/DoctrineMigrations/';
        $this->fs->mkdir([dirname($file), $versions, $this->root_dir.'app']);
        $this->putConfig($file);

        $version1 = 'Version55555555555555_Demo';
        $version2 = 'Version66666666666666_Test';
        touch($versions.$version1.'.php');
        touch($versions.$version2.'.php');

        $this->execute($config); // test

        $proxy_dir = $this->root_dir.'app/DoctrineMigrations/';
        $this->assertFileExists($proxy_dir.$version1.'.php');
        $this->assertFileExists($proxy_dir.$version2.'.php');
        $this->assertEquals($this->getVersionBody($version1), file_get_contents($proxy_dir.$version1.'.php'));
        $this->assertEquals($this->getVersionBody($version2), file_get_contents($proxy_dir.$version2.'.php'));
    }

    /**
     * @param string $version
     *
     * @return string
     */
    protected function getVersionBody($version)
    {
        return '<?php
namespace Application\Migrations;

use AnimeDb\Bundle\AnimeDbBundle\DoctrineMigrations\ProxyMigration;

require_once __DIR__."/../../vendor/foo/bar/DoctrineMigrations/'.$version.'.php";

class '.$version.' extends ProxyMigration
{
    protected function getMigration()
    {
        return new \Foo\Bundle\BarBundle\FooBarBundle\DoctrineMigrations\\'.$version.'($this->version);
    }
}';
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject|Package $package
     *
     * @return Up
     */
    protected function getJob($package)
    {
        return (new Up($package))->setRootDir($this->root_dir);
    }
}
