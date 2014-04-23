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
            // can not consistently perform the migration of one packet,
            // and then another because they may be dependent
            // to solve this problem create set of wrappers for sort migrations
            $config = $this->getNamespaceAndDirectory($config_file);

            // find migrations
            $finder = Finder::create()
                ->in(__DIR__.'/../../../../../../../vendor/'.$this->getPackage()->getName().'/'.$config['directory'])
                ->files()
                ->name('/Version\d{14}.*\.php/');

            $migdir = __DIR__.'/../../../../../../../app/DoctrineMigrations/';
            if ($finder->count() && !file_exists($migdir)) {
                mkdir($migdir);
            }

            /* @var $file \SplFileInfo */
            foreach ($finder as $file) {
                // create migration wrapper
                $version = $file->getBasename('.php');
                file_put_contents($migdir.$file->getBasename(), '<?php
namespace Application\Migrations;

use AnimeDb\Bundle\AnimeDbBundle\DoctrineMigrations\AbstractMigration;

require_once __DIR__."/../../vendor/'.$this->getPackage()->getName().'/'.$config['directory'].'/'.$file->getBasename().'";

class '.$version.' extends AbstractMigration
{
    protected function getMigrationClass()
    {
        return "'.$config['namespace'].'\\'.$version.'";
    }
}');
            }
        }
    }
}