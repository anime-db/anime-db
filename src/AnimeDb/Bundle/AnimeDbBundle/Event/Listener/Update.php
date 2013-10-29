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
 * Update listener
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Event\Listener
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Update
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
    const DEFAULT_PHP = 'php';

    /**
     * Default path to the directory with the application
     *
     * @var string
     */
    const DEFAULT_PATH = '.';

    /**
     * Add requirements in composer.json from old version
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded $event
     */
    public function onAppDownloadedMergeComposerRequirements(Downloaded $event)
    {
        $old_config = file_get_contents(__DIR__.'/../../../../../../composer.json');
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
     * Add requirements in AppKernal from old version
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded $event
     */
    public function onAppDownloadedMergeAppKernalBundles(Downloaded $event)
    {
        $old_kernel = __DIR__.'/../../../../../../app/AppKernel.php';
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
        $old_file = __DIR__.'/../../../../../../bin/Run.vbs';
        $new_file = $event->getPath().'/bin/Run.vbs';
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
        $old_file = __DIR__.'/../../../../../../bin/service';
        $new_file = $event->getPath().'/bin/service';
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