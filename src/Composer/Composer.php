<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer;

use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use Composer\Package\PackageInterface;
use Composer\Package\Loader\LoaderInterface;
use Composer\Json\JsonFile;
use Composer\Installer;

/**
 * Composer
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Composer
{
    /**
     * Factory
     *
     * @var \Composer\Factory
     */
    protected $factory;

    /**
     * Loader
     *
     * @var \Composer\Package\Loader\LoaderInterface
     */
    protected $loader;

    /**
     * Lock file composer.lock
     *
     * @var string
     */
    protected $lock_file;

    /**
     * Composer
     *
     * @var \Composer\Composer
     */
    protected $composer;

    /**
     * IO
     *
     * @var \Composer\IO\IOInterface
     */
    protected $io;

    /**
     * Construct
     *
     * @param \Composer\Factory $factory
     * @param \Composer\Package\Loader\LoaderInterface $loader
     * @param string $lock_file
     */
    public function __construct(Factory $factory, LoaderInterface $loader, $lock_file)
    {
        $this->factory = $factory;
        $this->loader = $loader;
        $this->lock_file = $lock_file;
    }

    /**
     * Get IO
     *
     * @return \Composer\IO\IOInterface
     */
    public function getIO()
    {
        if (!$this->io) {
            $this->io = new NullIO();
        }
        return $this->io;
    }

    /**
     * Set IO
     *
     * @param \Composer\IO\IOInterface $io
     */
    public function setIO(IOInterface $io)
    {
        if ($this->io !== $io) {
            $this->io = $io;
            if ($this->composer) {
                $this->reload();
            }
        }
    }

    /**
     * Get composer
     *
     * @return \Composer\Composer
     */
    protected function getComposer()
    {
        if (!$this->composer) {
            $this->reload();
        }
        return $this->composer;
    }

    /**
     * Reload Composer
     */
    public function reload()
    {
        // update application components to the latest version
        if (file_exists($this->lock_file)) {
            unlink($this->lock_file);
        }
        $this->composer = $this->factory->createComposer($this->getIO());
    }

    /**
     * Download
     *
     * @param \Composer\Package\PackageInterface $package
     * @param string $target
     */
    public function download(PackageInterface $package, $target)
    {
        $manager = $this->getComposer()->getDownloadManager();

        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $manager->setOutputProgress(false);
        }

        $manager
            ->getDownloaderForInstalledPackage($package)
            ->download($package, $target);
    }

    /**
     * Get root package
     *
     * @return \Composer\Package\RootPackageInterface
     */
    public function getRootPackage()
    {
        return $this->getComposer()->getPackage();
    }

    /**
     * Get package
     *
     * @throws \RuntimeException
     *
     * @param string $config
     *
     * @return \Composer\Package\RootPackage
     */
    public function getPackageFromConfigFile($config)
    {
        if (!file_exists($config)) {
            throw new \RuntimeException('File "'.$config.'" not found');
        }
        return $this->loader->load((new JsonFile($config))->read(), 'Composer\Package\RootPackage');
    }

    /**
     * Get installer
     *
     * @return \Composer\Installer
     */
    public function getInstaller()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->getComposer()->getDownloadManager()->setOutputProgress(false);
        }

        return Installer::create($this->getIO(), $this->getComposer())
            ->setDevMode(false)
            ->setPreferDist(true)
            ->setUpdate(true);
    }

    /**
     * Get version compatible
     *
     * 3.2.1-RC2 => 3.2.1.6.2
     *
     * @param string $version
     *
     * @return string|false
     */
    public static function getVersionCompatible($version)
    {
        // {suffix:weight}
        $suffixes = [
            'dev' => 1, // composer suffix
            'patch' => 2,
            'alpha' => 3,
            'beta' => 4,
            'stable' => 5, // is not a real suffix. use it if suffix is not exists
            'rc' => 6
        ];

        $reg = '/^v?(?<version>\d+\.\d+\.\d+)(?:-(?<suffix>dev|patch|alpha|beta|rc)(?<suffix_version>\d+)?)?$/i';
        if (!preg_match($reg, $version, $match)) {
            return false;
        }

        // suffix version
        if (isset($match['suffix'])) {
            $suffix = $suffixes[strtolower($match['suffix'])].'.';
            $suffix .= isset($match['suffix_version']) ? (int)$match['suffix_version'] : 1;
        } else {
            $suffix = $suffixes['stable'].'.0';
        }

        return $match['version'].'.'.$suffix;
    }
}