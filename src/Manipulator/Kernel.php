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
class Kernel extends FileContent
{
    /**
     * AppKernal content
     *
     * @var string
     */
    private $kernel;

    /**
     * AppKernal filename
     *
     * @var string
     */
    private $kernel_filename;

    /**
     * List of bundles
     *
     * @var array
     */
    private $bundles = null;

    /**
     * Construct
     *
     * @param string $filename
     * @param string $kernel_filename
     */
    public function __construct($filename, $kernel_filename)
    {
        parent::__construct($filename);
        $this->kernel_filename = $kernel_filename;
    }

    /**
     * Add bundle to kernal
     *
     * @param string $bundle
     */
    public function addBundle($bundle)
    {
        $bundle = 'new '.($bundle[0] == '\\' ? substr($bundle, 1) : $bundle).'()';
        $bundle = $this->getBundleTemplate($bundle);
        // not root bundle
        if (strpos($this->getKernal(), $bundle) === false) {
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
        $bundle = 'new '.($bundle[0] == '\\' ? substr($bundle, 1) : $bundle).'()';
        $bundles = $this->getBundles();
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
            $this->kernel = file_get_contents($this->kernel_filename);
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
            $content = $this->getContent();
            $start = strpos($content, '[');
            $bundles = trim(substr($content, $start+1, strpos($content, ']')-$start-1));
            if ($bundles) {
                $this->bundles = explode("\n", $bundles);
                foreach ($this->bundles as $key => $bundle) {
                    $this->bundles[$key] = trim($bundle, ' ,');
                }
            }
        }
        return $this->bundles;
    }

    /**
     * Set list of bundles
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
        $this->setContent($content);
    }
}