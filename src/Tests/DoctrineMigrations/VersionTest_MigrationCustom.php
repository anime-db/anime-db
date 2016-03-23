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

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

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
