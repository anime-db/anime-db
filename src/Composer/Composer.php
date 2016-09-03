<?php
/**
 * AnimeDb package.
 *
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
use Composer\Package\RootPackage;
use Composer\Package\RootPackageInterface;
use Composer\Composer as BaseComposer;

class Composer
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * Lock file composer.lock.
     *
     * @var string
     */
    protected $lock_file;

    /**
     * @var BaseComposer
     */
    protected $composer;

    /**
     * @var IOInterface
     */
    protected $io;

    /**
     * @param Factory $factory
     * @param LoaderInterface $loader
     * @param string $lock_file
     */
    public function __construct(Factory $factory, LoaderInterface $loader, $lock_file)
    {
        $this->factory = $factory;
        $this->loader = $loader;
        $this->lock_file = $lock_file;
    }

    /**
     * @return IOInterface
     */
    public function getIO()
    {
        if (!$this->io) {
            $this->io = new NullIO();
        }

        return $this->io;
    }

    /**
     * @param IOInterface $io
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
     * @return BaseComposer
     */
    protected function getComposer()
    {
        if (!$this->composer) {
            $this->reload();
        }

        return $this->composer;
    }

    /**
     * Reload Composer.
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
     * @param PackageInterface $package
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
     * @return RootPackageInterface
     */
    public function getRootPackage()
    {
        return $this->getComposer()->getPackage();
    }

    /**
     * @throws \RuntimeException
     *
     * @param string $config
     *
     * @return RootPackage
     */
    public function getPackageFromConfigFile($config)
    {
        if (!file_exists($config)) {
            throw new \RuntimeException('File "'.$config.'" not found');
        }

        return $this->loader->load((new JsonFile($config))->read(), 'Composer\Package\RootPackage');
    }

    /**
     * @return Installer
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
     * Get version compatible.
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
            'rc' => 6,
        ];

        $reg = '/^v?(?<version>\d+\.\d+\.\d+)(?:-(?<suffix>dev|patch|alpha|beta|rc)(?<suffix_version>\d+)?)?$/i';
        if (!preg_match($reg, $version, $match)) {
            return false;
        }

        // suffix version
        if (isset($match['suffix'])) {
            $suffix = $suffixes[strtolower($match['suffix'])].'.';
            $suffix .= isset($match['suffix_version']) ? (int) $match['suffix_version'] : 1;
        } else {
            $suffix = $suffixes['stable'].'.0';
        }

        return $match['version'].'.'.$suffix;
    }
}
