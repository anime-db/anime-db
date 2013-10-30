<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Package;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Package\Package as BasePackage;

/**
 * Job: Migrate package up
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Package
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Up extends BasePackage
{
    /**
     * (non-PHPdoc)
     * @see AnimeDb\Bundle\AnimeDbBundle\Composer\Job.Job::execute()
     */
    public function execute()
    {
        if ($config = $this->getMigrationsConfig()) {
           self::executeCommand('doctrine:migrations:migrate --no-interaction --configuration='.$config);
        }
    }
}