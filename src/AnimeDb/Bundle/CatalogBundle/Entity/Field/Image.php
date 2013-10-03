<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Entity\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator;

/**
 * Item images
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity\Field
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Image
{
    /**
     * Image from URL
     *
     * @Assert\Url()
     *
     * @var string
     */
    protected $remote;

    /**
     * Local image
     *
     * @Assert\File(
     *     maxSize = "2048k"
     * )
     * @Assert\Image(
     *     minWidth = 200,
     *     minHeight = 200,
     *     mimeTypes = {"image/bmp","image/gif","image/jpeg","image/png"},
     *     mimeTypesMessage = "Please upload a valid image file"
     * )
     *
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile|null
     */
    protected $local;

    /**
     * Path to image
     *
     * @var string
     */
    protected $path;

    /**
     * Set remote image
     *
     * @param string $remote
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Field\Image
     */
    public function setRemote($remote)
    {
        $this->remote = $remote;
        return $this;
    }

    /**
     * Get remote image
     *
     * @return string
     */
    public function getRemote()
    {
        return $this->remote;
    }

    /**
     * Set local image
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $local
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Field\Image
     */
    public function setLocal(UploadedFile $local)
    {
        $this->local = $local;
        return $this;
    }

    /**
     * Get local image
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getLocal()
    {
        return $this->local;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Upload image
     *
     * @param \Symfony\Component\Validator\Validator $validator
     * @param string|null $name
     */
    public function upload(Validator $validator, $name = null) {
        // upload remote file
        if ($this->getRemote() && $this->getLocal() === null) {
            if (!($content = file_get_contents($this->getRemote()))) {
                throw new \InvalidArgumentException('Unable to read remote file');
            }
            // download remote file
            $tempname = tempnam(sys_get_temp_dir(), 'php');
            file_put_contents($tempname, $content);

            // create local file from remote
            if (!($info = getimagesize($tempname))) {
                throw new \InvalidArgumentException('This is not a image file');
            }
            $originalName = pathinfo(parse_url($this->getRemote(), PHP_URL_PATH), PATHINFO_BASENAME);
            $this->remote = null;
            $this->setLocal(new UploadedFile(
                $tempname,
                $this->getUniqueFileName($this->getUploadRootDir().'/'.$originalName),
                $info['mime'],
                filesize($tempname),
                UPLOAD_ERR_OK,
                true
            ));

            // revalidate entity
            $errors = $validator->validate($this);
            if (count($errors)) {
                throw new \InvalidArgumentException($errors[0]->getMessage());
            }
        }

        // upload local file
        if ($this->getLocal() !== null) {
            if (!$name) {
                // upload from original name
                $name = $this->getUniqueFileName(
                    $this->getUploadRootDir().'/'.$this->getLocal()->getClientOriginalName()
                );
                $this->getLocal()->move($this->getUploadRootDir(), $name);
            } else {
                // upload to another name
                $info = pathinfo($name);
                $this->getLocal()->move($this->getUploadRootDir().'/'.$info['dirname'], $info['basename']);
            }
            $this->path = date('Y/m/').$name;
        }
    }

    /**
     * Is set image remote or local
     *
     * @Assert\True(message = "No selected image")
     * 
     * @return boolean
     */
    public function isSetImage()
    {
        return $this->remote || !is_null($this->local);
    }

    /**
     * Get absolute path
     *
     * @return string
     */
    public function getAbsolutePath()
    {
        return $this->path !== null ? $this->getUploadRootDir().'/../../'.$this->path : null;
    }

    /**
     * Get web path
     *
     * @return string
     */
    public function getWebPath()
    {
        return $this->path ? '/media/'.$this->path : null;
    }

    /**
     * Get upload root dir
     *
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../../web/'.$this->getUploadDir();
    }

    /**
     * Get upload dir
     *
     * @return string
     */
    protected function getUploadDir()
    {
        return 'media'.date('/Y/m');
    }

    /**
     * Get unique file name
     *
     * @param string $file Absolute path to file
     *
     * @return string
     */
    protected function getUniqueFileName($file) {
        $info = pathinfo($file);
        $name = $info['basename'];
        for ($i = 1; file_exists($info['dirname'].'/'.$name); $i++) {
            $name = $info['filename'].'['.$i.'].'.$info['extension'];
        }
        return $name;
    }
}