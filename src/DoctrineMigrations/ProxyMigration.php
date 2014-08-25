<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\DBAL\Schema\Schema;

/**
 * Proxy migration
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\DoctrineMigrations
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class ProxyMigration extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * Container
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * Origin migration
     *
     * @var \Doctrine\DBAL\Migrations\AbstractMigration
     */
    private $migration;

    /**
     * Set container
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Get origin migration
     *
     * @return \Doctrine\DBAL\Migrations\AbstractMigration
     */
    abstract protected function getMigration();

    /**
     * Get origin migration lazy load
     *
     * @return \Doctrine\DBAL\Migrations\AbstractMigration
     */
    private function getMigrationLazyLoad()
    {
        if (!($this->migration instanceof AbstractMigration)) {
            $this->migration = $this->getMigration();
            if ($this->migration instanceof ContainerAwareInterface) {
                $this->migration->setContainer($this->container);
            }
        }
        return $this->migration;
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::up()
     */
    public function up(Schema $schema)
    {
        $this->getMigrationLazyLoad()->up($schema);
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::preUp()
     */
    public function preUp(Schema $schema)
    {
        $this->getMigrationLazyLoad()->preUp($schema);
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::postUp()
     */
    public function postUp(Schema $schema)
    {
        $this->getMigrationLazyLoad()->postUp($schema);
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::down()
     */
    public function down(Schema $schema)
    {
        $this->getMigrationLazyLoad()->down($schema);
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::preDown()
     */
    public function preDown(Schema $schema)
    {
        $this->getMigrationLazyLoad()->preDown($schema);
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::postDown()
     */
    public function postDown(Schema $schema)
    {
        $this->getMigrationLazyLoad()->postDown($schema);
    }
}