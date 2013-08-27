<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Task for Task Scheduler
 *
 * @ORM\Entity
 * @ORM\Table(name="task", indexes={
 *   @ORM\Index(name="idx_task_next_start", columns={"next_run", "status"})
 * })
 * @ORM\HasLifecycleCallbacks
 * @Assert\Callback(methods={"isModifyValid"})
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDB\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Task
{
    /**
     * Disabled interval
     *
     * @var integer
     */
    const INTERVAL_DISABLED = 0;

    /**
     * Status enabled
     *
     * @var integer
     */
    const STATUS_ENABLED = 1;

    /**
     * Status interval
     *
     * @var integer
     */
    const STATUS_DISABLED = 0;

    /**
     * Command
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=128)
     *
     * @var string
     */
    protected $command;

    /**
     * Last run
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     *
     * @var \DateTime|null
     */
    protected $last_run;

    /**
     * Next run
     *
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     *
     * @var \DateTime
     */
    protected $next_run;

    /**
     * Interval run in seconds
     *
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer", message="The value {{ value }} is not a valid {{ type }}.")
     *
     * @var integer
     */
    protected $interval = self::INTERVAL_DISABLED;

    /**
     * A date/time string
     *
     * Valid formats are explained in Date and Time Formats.
     *
     * @ORM\Column(type="string", length=128)
     * @link http://www.php.net/manual/en/datetime.formats.php
     *
     * @var string
     */
    protected $modify;

    /**
     * Task status
     *
     * @ORM\Column(type="integer")
     * @Assert\Choice(callback = "getStatuses")
     *
     * @var integer
     */
    protected $status = self::STATUS_DISABLED;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->next_run = new \DateTime();
    }

    /**
     * Get supported statuses
     *
     * @return array
     */
    public static function getStatuses()
    {
        return [self::STATUS_DISABLED, self::STATUS_ENABLED];
    }

    /**
     * Set command
     *
     * @param string $command
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Task
     */
    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }

    /**
     * Get command
     *
     * @return string 
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Set last_run
     *
     * @param \DateTime $last_run
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Task
     */
    public function setLastRun($last_run)
    {
        $this->last_run = $last_run;
        return $this;
    }

    /**
     * Get last_run
     *
     * @return \DateTime 
     */
    public function getLastRun()
    {
        return $this->last_run;
    }

    /**
     * Set next_run
     *
     * @param \DateTime $next_run
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Task
     */
    public function setNextRun($next_run)
    {
        $this->next_run = $next_run;
        return $this;
    }

    /**
     * Get next_run
     *
     * @return \DateTime 
     */
    public function getNextRun()
    {
        return $this->next_run;
    }

    /**
     * Set interval
     *
     * @param integer $interval
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Task
     */
    public function setInterval($interval)
    {
        $this->interval = $interval;
        return $this;
    }

    /**
     * Get interval
     *
     * @return integer 
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * Set modify
     *
     * @param string $modify
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Task
     */
    public function setModify($modify)
    {
        $this->modify = $modify;
        return $this;
    }

    /**
     * Get modify
     *
     * @return string 
     */
    public function getModify()
    {
        return $this->modify;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Task
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Remove cover file
     *
     * @ORM\PreUpdate
     */
    public function doDisableIfCantRun()
    {
        if ($this->next_run->getTimestamp() < time()) {
            $this->status = self::STATUS_DISABLED;
        }
    }

    /**
     * Is valid modify date/time format
     *
     * @param \Symfony\Component\Validator\ExecutionContextInterface $context
     */
    public function isModifyValid(ExecutionContextInterface $context)
    {
        if ($this->getModify() && strtotime($this->getModify()) === false) {
            $context->addViolationAt('modify', 'Wrong date/time format');
        }
    }

    /**
     * Update entity after execution
     */
    public function executed()
    {
        $this->last_run = $this->next_run;
        // change next run date
        if ($this->interval != self::INTERVAL_DISABLED) {
            $this->next_run->setTimestamp(time() + $this->interval);
        } elseif ($this->modify) {
            $this->next_run->modify($this->modify);
        }
    }
}