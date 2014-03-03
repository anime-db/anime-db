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
    const DEFAULT_PHP = '"php"';

    /**
     * Default path to the directory with the application
     *
     * @var string
     */
    const DEFAULT_PATH = '.';

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
     * Add requirements in AppKernal from old version
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded $event
     */
    public function onAppDownloadedMergeAppKernalBundles(Downloaded $event)
    {
        $old_kernel = $this->root_dir.'app/AppKernel.php';
        $new_kernel = $event->getPath().'/app/AppKernel.php';
        if (md5_file($new_kernel) != md5_file($old_kernel)) {
            // get list of bundles
            $new_bundles = $this->getBundles($body = file_get_contents($new_kernel));
            $old_bundles = $this->getBundles(file_get_contents($old_kernel));

            if (array_intersect($new_bundles, $old_bundles) != $new_bundles) {
                $new_bundles = array_unique(array_merge($old_bundles, $new_bundles));
                // write all bundles into new AppKernel
                $start = strpos($body, '$bundles = [')+strlen('$bundles = [');
                $end = strpos($body, '];', $start);
                $new_bundles = "\n            ".implode(",\n            ", $new_bundles)."\n        ";
                file_put_contents($new_kernel, substr($body, 0, $start).$new_bundles.substr($body, $end));
            }
        }
    }

    /**
     * Merge bin Run.vbs commands
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded $event
     */
    public function onAppDownloadedMergeBinRun(Downloaded $event)
    {
        $old_file = $this->root_dir.'AnimeDB_Run.vbs';
        if (!file_exists($old_file)) { // old name
            $old_file = $this->root_dir.'bin/Run.vbs';
        }
        $new_file = $event->getPath().'/AnimeDB_Run.vbs';
        if (md5_file($old_file) != md5_file($new_file)) {
            $old_body = file_get_contents($old_file);
            $new_body = $tmp_body = file_get_contents($new_file);

            $new_body = $this->copyParam($old_body, $new_body, 'sAddr = "%s"', self::DEFAULT_ADDRESS);
            $new_body = $this->copyParam($old_body, $new_body, 'sPort = "%s"', self::DEFAULT_PORT);
            $new_body = $this->copyParam($old_body, $new_body, "sPhp = %s\n", self::DEFAULT_PHP);

            if ($new_body != $tmp_body) {
                file_put_contents($new_file, $new_body);
            }
        }
    }

    /**
     * Merge bin service commands
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

    /**
     * Get list of bundles from app kernel source
     *
     * @param string $kernel
     *
     * @return array
     */
    protected function getBundles($kernel)
    {
        $start = strpos($kernel, '$bundles = array(')+strlen('$bundles = array(');
        $end = strpos($kernel, ');', $start);
        $bundles = substr($kernel, $start, $end-$start);
        $bundles = explode("\n", $bundles);
        foreach ($bundles as $key => $bundle) {
            $bundles[$key] = trim($bundle, " ,");
        }
        return array_filter($bundles, 'trim');
    }
}