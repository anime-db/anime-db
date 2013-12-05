<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Installed plugin
 *
 * @ORM\Entity
 * @ORM\Table(name="plugin")
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDb\Bundle\AppBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Plugin
{
    /**
     * Name
     *
     * @ORM\Id
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $name;

    /**
     * Title
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $tilte;

    /**
     * Description
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $description;

    /**
     * Logo
     *
     * @ORM\Column(type="string", length=256, nullable=true)
     *
     * @var string
     */
    protected $logo = '';

    /**
     * Date install
     *
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     *
     * @var \DateTime
     */
    protected $date_install;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->date_install = new \DateTime();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return \AnimeDb\Bundle\AppBundle\Entity\Plugin
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set tilte
     *
     * @param string $tilte
     *
     * @return \AnimeDb\Bundle\AppBundle\Entity\Plugin
     */
    public function setTilte($tilte)
    {
        $this->tilte = $tilte;
        return $this;
    }

    /**
     * Get tilte
     *
     * @return string
     */
    public function getTilte()
    {
        return $this->tilte;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return \AnimeDb\Bundle\AppBundle\Entity\Plugin
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set logo
     *
     * @param string $logo
     *
     * @return \AnimeDb\Bundle\AppBundle\Entity\Plugin
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set date install
     *
     * @param \DateTime $date_install
     *
     * @return \AnimeDb\Bundle\AppBundle\Entity\Plugin
     */
    public function setDateInstall(\DateTime $date_install)
    {
        $this->date_install = $date_install;
        return $this;
    }

    /**
     * Get date install
     *
     * @return \DateTime
     */
    public function getDateInstall()
    {
        return $this->date_install;
    }

    /**
     * Get absolute path
     *
     * @return string
     */
    public function getAbsolutePath()
    {
        return $this->logo !== null ? $this->getUploadRootDir().'/'.$this->logo : null;
    }

    /**
     * Get upload root dir
     *
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    /**
     * Get upload dir
     *
     * @return string
     */
    protected function getUploadDir()
    {
        return 'media/'.$this->getName();
    }

    /**
     * Get logo web path
     *
     * @return string
     */
    public function getLogoWebPath()
    {
        return $this->logo ? '/'.$this->getUploadDir().'/'.$this->logo : null;
    }
}