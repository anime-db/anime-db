<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Console\Output;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

/**
 * Decorate console output for Windows
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Console\Output
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Windows implements ConsoleOutputInterface
{
    /**
     * Output
     *
     * @var \Symfony\Component\Console\Output\ConsoleOutputInterface
     */
    private $output;

    /**
     * Do encode messages
     *
     * @var boolean
     */
    private $encode = false;

    /**
     * Target encoding
     *
     * @var string
     */
    const TARGET_ENCODING = 'CP866';

    /**
     * Construct
     *
     * @param \Symfony\Component\Console\Output\ConsoleOutputInterface $output
     */
    public function __construct(ConsoleOutputInterface $output)
    {
        $this->output = $output;
        $this->encode = extension_loaded('mbstring');
        if ($this->encode && !in_array(self::TARGET_ENCODING, mb_detect_order())) {
            mb_detect_order(array_merge(mb_detect_order(), [self::TARGET_ENCODING]));
        }
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Output.OutputInterface::write()
     */
    public function write($messages, $newline = false, $type = self::OUTPUT_NORMAL)
    {
        $this->output->write($this->encode((array)$messages), $newline, $type);
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Output.OutputInterface::writeln()
     */
    public function writeln($messages, $type = self::OUTPUT_NORMAL)
    {
        $this->output->writeln($this->encode((array)$messages), $type);
    }

    /**
     * Encode messages
     *
     * @param array $messages
     *
     * @return array
     */
    protected function encode(array $messages)
    {
        if ($this->encode) {
            foreach ($messages as $key => $message) {
                if (($form = mb_detect_encoding($message)) != self::TARGET_ENCODING) {
                    $messages[$key] = mb_convert_encoding($message, self::TARGET_ENCODING, $form);
                }
            }
        }
        return $messages;
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Output.OutputInterface::setVerbosity()
     */
    public function setVerbosity($level)
    {
        $this->output->setVerbosity($level);
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Output.OutputInterface::getVerbosity()
     */
    public function getVerbosity()
    {
        return $this->output->getVerbosity();
    }

    /**
     * The current verbosity of the output is quiet
     *
     * @return boolean
     */
    public function isQuiet()
    {
        return self::VERBOSITY_QUIET === $this->getVerbosity();
    }

    /**
     * The current verbosity of the output is verbose
     *
     * @return boolean
     */
    public function isVerbose()
    {
        return self::VERBOSITY_VERBOSE <= $this->getVerbosity();
    }

    /**
     * The current verbosity of the output is very verbose
     *
     * @return boolean
     */
    public function isVeryVerbose()
    {
        return self::VERBOSITY_VERY_VERBOSE <= $this->getVerbosity();
    }

    /**
     * The current verbosity of the output is debug
     *
     * @return boolean
     */
    public function isDebug()
    {
        return self::VERBOSITY_DEBUG <= $this->getVerbosity();
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Output.OutputInterface::setDecorated()
     */
    public function setDecorated($decorated)
    {
        $this->output->setDecorated($decorated);
    }

    /**
     * Gets the decorated flag.
     *
     * @return Boolean true if the output will decorate messages, false otherwise
     *
     * @api
     */
    public function isDecorated()
    {
        return $this->output->isDecorated();
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Output.OutputInterface::setFormatter()
     */
    public function setFormatter(OutputFormatterInterface $formatter)
    {
        $this->output->setFormatter($formatter);
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Output.OutputInterface::getFormatter()
     */
    public function getFormatter()
    {
        return $this->output->getFormatter();
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Output.ConsoleOutputInterface::getErrorOutput()
     */
    public function getErrorOutput()
    {
        return $this->output->getErrorOutput();
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Output.ConsoleOutputInterface::setErrorOutput()
     */
    public function setErrorOutput(OutputInterface $error)
    {
        $this->output->setErrorOutput($error);
    }
}
