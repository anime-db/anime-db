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
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Job: Migrate package down
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Down extends BaseMigrate
{
    /**
     * (non-PHPdoc)
     * @see AnimeDb\Bundle\AnimeDbBundle\Composer\Job.Job::setContainer()
     */
    public function setContainer(Container $container)
    {
        parent::setContainer($container);

        // migrate down before uninstall package
        if ($config_file = $this->getMigrationsConfig()) {
            // can not consistently perform the migration of one packet,
            // and then another because they may be dependent
            // also the migration files will be deleted after the package is removed
            // to solve this problem copy the migrations files to a temporary directory for later execution

            $config = $this->getNamespaceAndDirectory($config_file);

            // find migrations
            $from = __DIR__.'/../../../../vendor/'.$this->getPackage()->getName().'/'.$config['directory'];
            $package_migrations = Finder::create()
                ->in($from)
                ->files()
                ->name('/Version\d{14}.*\.php/');

            if ($package_migrations->count()) {
                // remove wrappers of migrations
                $migdir = __DIR__.'/../../../../app/DoctrineMigrations/';
                if (file_exists($migdir)) {
                    /* @var $file \SplFileInfo */
                    foreach ($package_migrations as $file) {
                        @unlink($migdir.$file->getBasename());
                    }
                }

                // copy the migrations to perform later
                $tmp_dir = __DIR__.'/../../../../app/cache/dev/DoctrineMigrations/';
                $fs = new Filesystem();
                $fs->mirror($from, $tmp_dir);

                // change migrations namespace
                $tmp_migrations = Finder::create()
                    ->in($tmp_dir)
                    ->files()
                    ->name('/Version\d{14}.*\.php/');
                /* @var $file \SplFileInfo */
                foreach ($tmp_migrations as $file) {
                    $migration = file_get_contents($file->getPathname());
                    $migration = preg_replace('/namespace [^;]+;/', 'namespace Application\Migrations;', $migration);
                    file_put_contents($file->getPathname(), $migration);
                }
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see AnimeDb\Bundle\AnimeDbBundle\Composer\Job.Job::execute()
     */
    public function execute()
    {
        // job will be executed later 
        // @see \AnimeDb\Bundle\AnimeDbBundle\Composer\ScriptHandler::migrateDown()
    }
}