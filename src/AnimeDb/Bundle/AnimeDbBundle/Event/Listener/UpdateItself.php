<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Event\Listener;

use AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded;
use Sensio\Bundle\GeneratorBundle\Manipulator\KernelManipulator;

/**
 * Update itself listener
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Event\Listener
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class UpdateItself
{
    /**
     * Default address
     *
     * @var string
     */
    const DEFAULT_ADDRESS = '0.0.0.0';

    /**
     * Default port
     *
     * @var string
     */
    const DEFAULT_PORT = '56780';

    /**
     * Default path to php executer
     *
     * @var string
     */
    const DEFAULT_PHP = './bin\\php\\php.exe';

    /**
     * Default path to the directory with the application
     *
     * @var string
     */
    const DEFAULT_PATH = '.';

    /**
     * Link to monitor archive
     *
     * @var string
     */
    const MONITOR = 'http://anime-db.org/download/monitor_1.0.zip';

    /**
     * Root dir
     *
     * @var string
     */
    protected $root_dir = '';

    /**
     * Construct
     *
     * @param string $kernel_root_dir
     */
    public function __construct($kernel_root_dir)
    {
        $this->root_dir = $kernel_root_dir.'/../';
    }

    /**
     * Add requirements in composer.json from old version
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded $event
     */
    public function onAppDownloadedMergeComposerRequirements(Downloaded $event)
    {
        $old_config = file_get_contents($this->root_dir.'composer.json');
        $old_config = json_decode($old_config, true);

        $new_config = file_get_contents($event->getPath().'/composer.json');
        $new_config = json_decode($new_config, true);

        if ($old_config['require'] != $new_config['require']) {
            $new_config['require'] = array_merge($old_config['require'], $new_config['require']);
            $new_config = json_encode($new_config, JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
            file_put_contents($event->getPath().'/composer.json', $new_config);
        }
    }

    /**
     * Copy configs from old version
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded $event
     */
    public function onAppDownloadedMergeConfigs(Downloaded $event)
    {
        $files = [
            '/app/config/parameters.yml',
            '/app/config/vendor_config.yml',
            '/app/config/routing.yml',
            '/app/bundles.php'
        ];

        foreach ($files as $file) {
            if (file_exists($this->root_dir.$file)) {
                copy($this->root_dir.$file, $event->getPath().$file);
            }
        }
    }

    /**
     * Merge bin AnimeDB_Run.vbs commands
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded $event
     */
    public function onAppDownloadedMergeBinRun(Downloaded $event)
    {
        // remove startup files
        @unlink($this->root_dir.'bin/AnimeDB_Run.vbs');
        @unlink($this->root_dir.'bin/AnimeDB_Stop.vbs');
        @unlink($this->root_dir.'AnimeDB_Run.vbs');
        @unlink($this->root_dir.'AnimeDB_Stop.vbs');

        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            // download monitor if need
            if (!file_exists($this->root_dir.'/config.ini')) {
                $monitor = tempnam(sys_get_temp_dir(), 'monitor');
                file_put_contents($monitor, fopen(self::MONITOR, 'r'));
                // unzip
                $zip = new \ZipArchive();
                if ($zip->open($monitor) !== true) {
                    throw new \RuntimeException('Failed unzip monitor');
                }
                $zip->extractTo($event->getPath());
                $zip->close();
            }

            // copy params if need
            $old_file = $this->root_dir.'/config.ini';
            $new_file = $event->getPath().'/config.ini';
            if (file_exists($old_file) && md5_file($old_file) != md5_file($new_file)) {
                $old_body = file_get_contents($old_file);
                $new_body = $tmp_body = file_get_contents($new_file);

                $new_body = $this->copyParam($old_body, $new_body, "addr=%s\n", self::DEFAULT_ADDRESS);
                $new_body = $this->copyParam($old_body, $new_body, "port=%s\n", self::DEFAULT_PORT);
                $new_body = $this->copyParam($old_body, $new_body, "php=%s\n", self::DEFAULT_PHP);

                if ($new_body != $tmp_body) {
                    file_put_contents($new_file, $new_body);
                }
            }
        }
    }

    /**
     * Merge bin AnimeDB commands
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded $event
     */
    public function onAppDownloadedMergeBinService(Downloaded $event)
    {
        $old_file = $this->root_dir.'AnimeDB';
        if (!file_exists($old_file)) { // old name
            $old_file = $this->root_dir.'bin/service';
        }
        $new_file = $event->getPath().'/AnimeDB';
        if (md5_file($old_file) != md5_file($new_file)) {
            $old_body = file_get_contents($old_file);
            $new_body = $tmp_body = file_get_contents($new_file);

            $new_body = $this->copyParam($old_body, $new_body, "addr='%s'", self::DEFAULT_ADDRESS);
            $new_body = $this->copyParam($old_body, $new_body, "port=%s\n", self::DEFAULT_PORT);
            $new_body = $this->copyParam($old_body, $new_body, "path=%s\n", self::DEFAULT_PATH);

            if ($new_body != $tmp_body) {
                file_put_contents($new_file, $new_body);
            }
        }
    }

    /**
     * Copy param value if need
     *
     * @param string $from
     * @param string $target
     * @param string $param
     * @param string $default
     *
     * @return string
     */
    protected function copyParam($from, $target, $param, $default)
    {
        // param has been changed
        if (strpos($from, sprintf($param, $default)) === false) {
            list($left, $right) = explode('%s', $param);
            $start = strpos($from, $left)+strlen($left); 
            $end = strpos($from, $right, $start);
            $value = substr($from, $start, $end-$start);
            $target = str_replace(sprintf($param, $default), sprintf($param, $value), $target);
        }
        return $target;
    }
}