<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Tests\DoctrineMigrations;

use AnimeDb\Bundle\AnimeDbBundle\DoctrineMigrations\ProxyMigration;
use Doctrine\DBAL\Schema\Schema;

class ProxyMigrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ProxyMigration
     */
    protected $proxy;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Schema
     */
    protected $schema;

    protected function setUp()
    {
        $this->schema = $this->getMock('\Doctrine\DBAL\Schema\Schema');
        $this->proxy = $this->getMockForAbstractClass(
            '\AnimeDb\Bundle\AnimeDbBundle\DoctrineMigrations\ProxyMigration',
            [],
            '',
            false
        );
    }

    public function getMethod()
    {
        return [
            ['up'],
            ['preUp'],
            ['postUp'],
            ['down'],
            ['preDown'],
            ['postDown'],
        ];
    }

    /**
     * @dataProvider getMethod
     */
    public function testMigration($method)
    {
        $migration = $this
            ->getMockBuilder('\Doctrine\DBAL\Migrations\AbstractMigration')
            ->disableOriginalConstructor()
            ->getMock();
        $this->proxy
            ->expects($this->any())
            ->method('getMigration')
            ->will($this->returnValue($migration));
        $migration
            ->expects($this->once())
            ->method($method)
            ->with($this->schema);
        call_user_func([$this->proxy, $method], $this->schema);
    }

    /**
     * @dataProvider getMethod
     */
    public function testMigrationContainerAware($method)
    {
        $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
        $migration = $this
            ->getMockBuilder('\\'.__NAMESPACE__.'\VersionTestMigrationCustom')
            ->disableOriginalConstructor()
            ->getMock();
        $this->proxy->setContainer($container);
        $this->proxy
            ->expects($this->once())
            ->method('getMigration')
            ->will($this->returnValue($migration));
        $migration
            ->expects($this->once())
            ->method($method)
            ->with($this->schema);
        $migration
            ->expects($this->once())
            ->method('setContainer')
            ->with($container);
        call_user_func([$this->proxy, $method], $this->schema);
    }
}
