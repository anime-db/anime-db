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

use Doctrine\DBAL\Migrations\AbstractMigration as BaseMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\DBAL\Schema\Schema;

/**
 * Bundle
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\DoctrineMigrations
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class AbstractMigration extends BaseMigration implements ContainerAwareInterface
{
    /**
     * Container
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * Origin migration
     *
     * @var \Doctrine\DBAL\Migrations\AbstractMigration
     */
    protected $migration;

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
     * Get origin migration class
     *
     * @return string
     */
    abstract protected function getMigrationClass();

    /**
     * Get class of origin migration
     *
     * @return \Doctrine\DBAL\Migrations\AbstractMigration
     */
    protected function getMigration()
    {
        if (!($this->migration instanceof BaseMigration)) {
            $class_name = $this->getMigrationClass();
            $this->migration = new $class_name($this->version);
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
        $this->getMigration()->up($schema);
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::preUp()
     */
    public function preUp(Schema $schema)
    {
        $this->getMigration()->preUp($schema);
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::postUp()
     */
    public function postUp(Schema $schema)
    {
        $this->getMigration()->postUp($schema);
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::down()
     */
    public function down(Schema $schema)
    {
        $this->getMigration()->down($schema);
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::preDown()
     */
    public function preDown(Schema $schema)
    {
        $this->getMigration()->preDown($schema);
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::postDown()
     */
    public function postDown(Schema $schema)
    {
        $this->getMigration()->postDown($schema);
    }
}