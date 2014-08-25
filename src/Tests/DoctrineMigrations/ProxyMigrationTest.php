<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\DoctrineMigrations;

use AnimeDb\Bundle\AnimeDbBundle\DoctrineMigrations\ProxyMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Test proxy migration
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\DoctrineMigrations
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ProxyMigrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Proxy migration
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $proxy;

    /**
     * Schema
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $schema;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $this->schema = $this->getMock('\Doctrine\DBAL\Schema\Schema');
        $this->proxy = $this->getMockForAbstractClass(
            '\AnimeDb\Bundle\AnimeDbBundle\DoctrineMigrations\ProxyMigration',
            [],
            '',
            false
        );
    }

    /**
     * Data provider
     */
    public function getMethod()
    {
        return [
            ['up'],
            ['preUp'],
            ['postUp'],
            ['down'],
            ['preDown'],
            ['postDown']
        ];
    }

    /**
     * @dataProvider getMethod
     */
    public function testMigration($method)
    {
        $migration = $this->getMockBuilder('\Doctrine\DBAL\Migrations\AbstractMigration')
            ->disableOriginalConstructor()
            ->getMock();
        $this->proxy
            ->expects($this->any())
            ->method('getMigration')
            ->willReturn($migration);
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
        $migration = $this->getMockBuilder('\\'.__NAMESPACE__.'\VersionTest_MigrationCustom')
            ->disableOriginalConstructor()
            ->getMock();
        $this->proxy->setContainer($container);
        $this->proxy
            ->expects($this->once())
            ->method('getMigration')
            ->willReturn($migration);
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

/**
 * Migration with custom name
 */
class VersionTest_MigrationCustom extends AbstractMigration implements ContainerAwareInterface
{
    public function setContainer(ContainerInterface $container = null) {}
    public function down(Schema $schema) {}
    public function postDown(Schema $schema) {}
    public function postUp(Schema $schema) {}
    public function preDown(Schema $schema) {}
    public function preUp(Schema $schema) {}
    public function up(Schema $schema) {}
}