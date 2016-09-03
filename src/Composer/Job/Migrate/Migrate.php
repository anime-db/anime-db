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

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;
use Symfony\Component\Yaml\Yaml;

abstract class Migrate extends Job
{
    /**
     * @var int
     */
    const PRIORITY = self::PRIORITY_INIT;

    /**
     * Get path to migrations config file from package
     *
     * @return string|null
     */
    protected function getMigrationsConfig()
    {
        $dir = $this->getPackageDir();

        // specific location
        if ($migrations = $this->getPackageOptionFile('anime-db-migrations')) {
            return $dir.$migrations;
        }

        if (file_exists($dir.'migrations.yml')) {
            return $dir.'migrations.yml';
        } elseif (file_exists($dir.'migrations.xml')) {
            return $dir.'migrations.xml';
        }

        return null;
    }

    /**
     * Parse config file
     *
     * Return:
     * <code>
     * {
     *   namespace: string,
     *   directory: string
     * }
     * </code>
     *
     * @param string $file
     *
     * @return array
     */
    protected function parseConfig($file)
    {
        $namespace = '';
        $directory = '';

        $config = file_get_contents($file);
        switch (pathinfo($file, PATHINFO_EXTENSION)) {
            case 'yml':
            case 'yaml':
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
                $list = $doc->getElementsByTagName('migrations-namespace');
                if ($list->length) {
                    $namespace = $list->item(0)->nodeValue;
                }
                $list = $doc->getElementsByTagName('migrations-directory');
                if ($list->length) {
                    $directory = $list->item(0)->nodeValue;
                }
                break;
        }

        return [
            'namespace' => $namespace && $namespace[0] == '\\' ? substr($namespace, 1) : $namespace,
            'directory' => $directory
        ];
    }
}
