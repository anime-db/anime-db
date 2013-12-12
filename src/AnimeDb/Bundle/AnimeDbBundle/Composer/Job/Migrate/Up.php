<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Migrate as BaseMigrate;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\Finder;

/**
 * Job: Migrate package up
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Up extends BaseMigrate
{
    /**
     * (non-PHPdoc)
     * @see AnimeDb\Bundle\AnimeDbBundle\Composer\Job.Job::execute()
     */
    public function execute()
    {
        if ($config_file = $this->getMigrationsConfig()) {
            $config = $this->getNamespaceAndDirectory($config_file);

            // find migrations
            $finder = Finder::create()
                ->in(__DIR__.'/../../../../../../../vendor/'.$this->getPackage()->getName().'/'.$config['directory'])
                ->files()
                ->name('/Version\d{14}.*\.php/');

            /* @var $file \SplFileInfo */
            foreach ($finder as $file) {
                // create migration wrapper
                $version = $file->getBasename('.php');
                file_put_contents(__DIR__.'/../../../../../../../app/DoctrineMigrations/'.$file->getBasename(), '<?php
namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use '.$config['namespace'].'\\'.$version.' as Migration;

require_once __DIR__."/../../vendor/'.$this->getPackage()->getName().'/'.$config['directory'].'/'.$file->getBasename().'";

class '.$version.' extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $migration = new Migration($this->version);
        $migration->up($schema);
    }

    public function down(Schema $schema)
    {
    }
}');
            }
        }
    }

    /**
     * Get migrations namespace and directory
     *
     * @param string $file
     *
     * @return array {namespace:string, directory:string}
     */
    protected function getNamespaceAndDirectory($file)
    {
        $namespace = '';
        $directory = '';

        $config = file_get_contents($file);
        switch (pathinfo($file, PATHINFO_EXTENSION)) {
            case 'yml':
                $config = Yaml::parse($config);
                if (isset($config['migrations_namespace'])) {
                    $namespace = $config['migrations_namespace'];
                }
                if (isset($config['migrations_directory'])) {
                    $directory = $config['migrations_directory'];
                }
                break;
            case 'xml':
                $doc = new \DOMDocument();
                $doc->loadXML($config);
                $xpath = new \DOMXPath($doc);
                $list = $xpath->query('/doctrine-migrations/migrations-namespace');
                if ($list->length) {
                    $namespace = $list->item(0)->nodeValue;
                }
                $list = $xpath->query('/doctrine-migrations/migrations-directory');
                if ($list->length) {
                    $directory = $list->item(0)->nodeValue;
                }
                break;
        }

        return [
            'namespace' => !$namespace || $namespace[0] == '\\' ? $namespace : '\\'.$namespace,
            'directory' => $directory
        ];
    }
}