<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Migrate as BaseMigrate;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Job: Migrate package down.
 */
class Down extends BaseMigrate
{
    public function register()
    {
        // migrate down before uninstall package
        if ($config_file = $this->getMigrationsConfig()) {
            // can not consistently perform the migration of one packet,
            // and then another because they may be dependent
            // also the migration files will be deleted after the package is removed
            // to solve this problem copy the migrations files to a temporary directory for later execution

            $config = $this->parseConfig($config_file);

            // find migrations
            $from = $this->root_dir.'vendor/'.$this->getPackage()->getName().'/'.$config['directory'];
            $package_migrations = Finder::create()
                ->in($from)
                ->files()
                ->name('/Version\d{14}.*\.php/');

            if ($package_migrations->count()) {
                $fs = new Filesystem();
                // remove wrappers of migrations
                $migdir = $this->root_dir.'app/DoctrineMigrations/';
                if ($fs->exists($migdir)) {
                    /* @var $file \SplFileInfo */
                    foreach ($package_migrations as $file) {
                        $fs->remove($migdir.$file->getBasename());
                    }
                }

                // copy the migrations to perform later
                $tmp_dir = $this->root_dir.'app/cache/dev/DoctrineMigrations/';
                $fs->mirror($from, $tmp_dir);

                // change migrations namespace
                foreach ($package_migrations as $file) {
                    $migration = file_get_contents($tmp_dir.$file->getBasename());
                    $migration = preg_replace('/namespace [^;]+;/', 'namespace Application\Migrations;', $migration);
                    file_put_contents($tmp_dir.$file->getBasename(), $migration);
                }
            }
        }
    }

    public function execute()
    {
        /*
         * Job will be executed later
         * @see \AnimeDb\Bundle\AnimeDbBundle\Composer\ScriptHandler::migrateDown()
         */
    }
}
