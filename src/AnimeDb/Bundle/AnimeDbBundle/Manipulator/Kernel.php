<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Manipulator;

/**
 * Kernel manipulator
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Manipulator
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Kernel
{
    /**
     * AppKernal content
     *
     * @var string
     */
    private $kernel;

    /**
     * List of bundles
     *
     * @var array
     */
    private $bundles = null;

    /**
     * Add bundle to kernal
     *
     * @param string $bundle
     */
    public function addBundle($bundle)
    {
        // not root bundle
        if (strpos($this->getKernal(), substr($bundle, 1)) === false) {
            $bundle = 'new '.substr($bundle, 1).'()';
            $bundles = $this->getBundles();
            if (!in_array($bundle, $bundles)) {
                $bundles[] = $bundle;
                $this->setBundles($bundles);
            }
        }
    }

    /**
     * Remove bundle from kernal
     *
     * @param string $bundle
     */
    public function removeBundle($bundle)
    {
        $bundles = $this->getBundles();
        $bundle = 'new '.substr($bundle, 1).'()';
        if (($key = array_search($bundle, $bundles)) !== false) {
            unset($bundles[$key]);
            $this->setBundles($bundles);
        }
    }

    /**
     * Get AppKernal content
     *
     * @return string
     */
    protected function getKernal()
    {
        if (!$this->kernel) {
            $this->kernel = file_get_contents(__DIR__.'/../../../../../app/AppKernel.php');
        }
        return $this->kernel;
    }

    /**
     * Get list of bundles
     *
     * @return array
     */
    protected function getBundles()
    {
        if (is_null($this->bundles)) {
            $this->bundles = [];
            $content = file_get_contents(__DIR__.'/../../../../../app/bundles.php');
            $start = strpos($content, '[');
            $this->bundles = substr($content, $start+1, strpos($content, ']')-$start-1);
            $this->bundles = explode("\n", trim($this->bundles));
            foreach ($this->bundles as $key => $bundle) {
                $this->bundles[$key] = trim($bundle, ' ,');
            }
        }
        return $this->bundles;
    }

    /**
     * 
     * @param array $bundles
     */
    protected function setBundles(array $bundles)
    {
        $this->bundles = $bundles;
        if ($bundles) {
            $content = "<?php\nreturn [\n    ".implode(",\n    ", $bundles)."\n];";
        } else {
            $content = "<?php\nreturn [\n];";
        }
        file_put_contents(__DIR__.'/../../../../../app/bundles.php', $content);
    }
}