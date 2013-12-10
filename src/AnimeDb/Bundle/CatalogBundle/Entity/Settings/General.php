<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Entity\Settings;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * General Settings
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity\Settings
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class General
{
    /**
     * Serial number
     *
     * TODO temporarily disabled #69
     * @ Assert\NotBlank()
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
     * Plugin default search to fill
     *
     * @var string
     */
    protected $default_search;

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
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Settings\General
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
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Settings\General
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
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Settings\General
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Get plugin default search to fill
     * 
     * @return string
     */
    public function getDefaultSearch()
    {
        return $this->default_search;
    }

    /**
     * Set plugin default search to fill
     *
     * @param string $default_search
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Settings\General
     */
    public function setDefaultSearch($default_search)
    {
        $this->default_search = $default_search;
        return $this;
    }
}