<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Entity\Settings;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * General Settings
 *
 * @package AnimeDB\Bundle\CatalogBundle\Entity\Settings
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class General
{
    /**
     * Serial number
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^([A-Z0-9]{4}\-[A-Z0-9]{4}\-[A-Z0-9]{4}\-[A-Z0-9]{4})$/",
     *     message="Serial number must consist of numbers and letters, and have the form of XXXX-XXXX-XXXX-XXXX"
     * )
     *
     * @var string
     */
    protected $serial_number;

    /**
     * Task scheduler
     *
     * @Assert\Type(type="bool", message="The value {{ value }} is not a valid {{ type }}.")
     *
     * @var string
     */
    protected $task_scheduler = true;

    /**
     * Locale
     *
     * @Assert\Locale
     *
     * @var string
     */
    protected $locale;

    /**
     * Get serial number
     * 
     * @return string
     */
    public function getSerialNumber()
    {
        return $this->serial_number;
    }

    /**
     * Set serial number
     *
     * @param string $serial_number
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Settings\General
     */
    public function setSerialNumber($serial_number)
    {
        $this->serial_number = $serial_number;
        return $this;
    }

    /**
     * Get task scheduler
     * 
     * @return string
     */
    public function getTaskScheduler()
    {
        return $this->task_scheduler;
    }

    /**
     * Set task scheduler
     *
     * @param string $task_scheduler
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Settings\General
     */
    public function setTaskScheduler($task_scheduler)
    {
        $this->task_scheduler = $task_scheduler;
        return $this;
    }

    /**
     * Get locale
     * 
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set locale
     *
     * @param string $locale
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Settings\General
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }
}