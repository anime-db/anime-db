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
        if ($config = $this->getMigrationsConfig()) {
            $container->executeCommand('doctrine:migrations:migrate 0 --no-interaction --configuration='.$config);
        }
    }

    /**
     * (non-PHPdoc)
     * @see AnimeDb\Bundle\AnimeDbBundle\Composer\Job.Job::execute()
     */
    public function execute()
    {
        // job already executed
    }
}