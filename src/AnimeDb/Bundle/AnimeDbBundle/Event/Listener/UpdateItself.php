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

        if (array_intersect($old_config['require'], $new_config['require']) != $new_config['require']) {
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