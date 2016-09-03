<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Console\Output;

use AnimeDb\Bundle\AnimeDbBundle\Console\Output\Windows;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WindowsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ConsoleOutputInterface
     */
    protected $output;

    /**
     * @var Windows
     */
    protected $windows;

    /**
     * mb_detect_order.
     *
     * @var array
     */
    protected $charset = [];

    protected function setUp()
    {
        if (!extension_loaded('mbstring')) {
            $this->markTestSkipped('Extension "mbstring" is not loaded');
        }

        $this->charset = mb_detect_order();
        $this->output = $this->getMock('\Symfony\Component\Console\Output\ConsoleOutputInterface');
        $this->windows = new Windows($this->output);
    }

    public function tearDown()
    {
        mb_detect_order($this->charset);
    }

    public function testChangeDetectOrder()
    {
        mb_detect_order(['UTF-8']);
        new Windows($this->output);
        $this->assertEquals(['UTF-8', Windows::TARGET_ENCODING], mb_detect_order());

        mb_detect_order(['UTF-8', Windows::TARGET_ENCODING]);
        new Windows($this->output);
        $this->assertEquals(['UTF-8', Windows::TARGET_ENCODING], mb_detect_order());
    }

    /**
     * @return array
     */
    public function getDataForWrite()
    {
        return [
            [
                'foo',
                true,
                OutputInterface::OUTPUT_NORMAL,
                ['foo'],
                true,
                OutputInterface::OUTPUT_NORMAL,
            ],
            [
                ['foo', 'bar'],
                false,
                OutputInterface::OUTPUT_PLAIN,
                ['foo', 'bar'],
                false,
                OutputInterface::OUTPUT_PLAIN,
            ],
            [
                '日本',
                true,
                OutputInterface::OUTPUT_RAW,
                [mb_convert_encoding('日本', Windows::TARGET_ENCODING, 'UTF-8')],
                true,
                OutputInterface::OUTPUT_RAW,
            ],
        ];
    }

    /**
     * @dataProvider getDataForWrite
     *
     * @param string $messages
     * @param bool $newline
     * @param int $type
     * @param string $expected_messages
     * @param bool $expected_newline
     * @param int $expected_type
     */
    public function testWrite(
        $messages,
        $newline,
        $type,
        $expected_messages,
        $expected_newline,
        $expected_type
    ) {
        $this->output
            ->expects($this->once())
            ->method('write')
            ->with($expected_messages, $expected_newline, $expected_type);
        $this->windows->write($messages, $newline, $type);
    }

    /**
     * @return array
     */
    public function getDataForWriteLn()
    {
        return [
            [
                'foo',
                OutputInterface::OUTPUT_NORMAL,
                ['foo'],
                OutputInterface::OUTPUT_NORMAL,
            ],
            [
                ['foo', 'bar'],
                OutputInterface::OUTPUT_PLAIN,
                ['foo', 'bar'],
                OutputInterface::OUTPUT_PLAIN,
            ],
            [
                '日本',
                OutputInterface::OUTPUT_RAW,
                [mb_convert_encoding('日本', Windows::TARGET_ENCODING, 'UTF-8')],
                OutputInterface::OUTPUT_RAW,
            ],
        ];
    }

    /**
     * @dataProvider getDataForWriteLn
     *
     * @param string $messages
     * @param string $type
     * @param string $expected_messages
     * @param string $expected_type
     */
    public function testWriteLn($messages, $type, $expected_messages, $expected_type)
    {
        $this->output
            ->expects($this->once())
            ->method('writeLn')
            ->with($expected_messages, $expected_type);
        $this->windows->writeLn($messages, $type);
    }

    /**
     * @return array
     */
    public function getProxyMethods()
    {
        $formatter = $this->getMock('\Symfony\Component\Console\Formatter\OutputFormatterInterface');
        $output = $this->getMock('\Symfony\Component\Console\Output\OutputInterface');

        return [
            ['setVerbosity', 'getVerbosity', 1],
            ['setDecorated', 'isDecorated', true],
            ['setFormatter', 'getFormatter', $formatter],
            ['setErrorOutput', 'getErrorOutput', $output],
        ];
    }

    /**
     * @dataProvider getProxyMethods
     *
     * @param string $set
     * @param string $get
     * @param mixed $expected
     */
    public function testProxyMethods($set, $get, $expected)
    {
        // set
        $this->output
            ->expects($this->once())
            ->method($set)
            ->with($expected);
        call_user_func([$this->windows, $set], $expected);

        // get
        $this->output
            ->expects($this->once())
            ->method($get)
            ->will($this->returnValue($expected));
        $this->assertEquals($expected, call_user_func([$this->windows, $get]));
    }

    /**
     * @return array
     */
    public function getVerbosity()
    {
        return [
            [
                'isQuiet',
                OutputInterface::VERBOSITY_QUIET,
                true,
            ],
            [
                'isQuiet',
                OutputInterface::VERBOSITY_NORMAL,
                false,
            ],
            [
                'isVerbose',
                OutputInterface::VERBOSITY_QUIET,
                false,
            ],
            [
                'isVerbose',
                OutputInterface::VERBOSITY_NORMAL,
                false,
            ],
            [
                'isVerbose',
                OutputInterface::VERBOSITY_VERBOSE,
                true,
            ],
            [
                'isVerbose',
                OutputInterface::VERBOSITY_VERY_VERBOSE,
                true,
            ],
            [
                'isVerbose',
                OutputInterface::VERBOSITY_DEBUG,
                true,
            ],
            [
                'isVeryVerbose',
                OutputInterface::VERBOSITY_QUIET,
                false,
            ],
            [
                'isVeryVerbose',
                OutputInterface::VERBOSITY_NORMAL,
                false,
            ],
            [
                'isVeryVerbose',
                OutputInterface::VERBOSITY_VERBOSE,
                false,
            ],
            [
                'isVeryVerbose',
                OutputInterface::VERBOSITY_VERY_VERBOSE,
                true,
            ],
            [
                'isVeryVerbose',
                OutputInterface::VERBOSITY_DEBUG,
                true,
            ],
            [
                'isDebug',
                OutputInterface::VERBOSITY_QUIET,
                false,
            ],
            [
                'isDebug',
                OutputInterface::VERBOSITY_NORMAL,
                false,
            ],
            [
                'isDebug',
                OutputInterface::VERBOSITY_VERBOSE,
                false,
            ],
            [
                'isDebug',
                OutputInterface::VERBOSITY_VERY_VERBOSE,
                false,
            ],
            [
                'isDebug',
                OutputInterface::VERBOSITY_DEBUG,
                true,
            ],
        ];
    }

    /**
     * @dataProvider getVerbosity
     *
     * @param string $method
     * @param int $verbosity
     * @param bool $expected
     */
    public function testIsVerbosity($method, $verbosity, $expected)
    {
        // set
        $this->output
            ->expects($this->once())
            ->method('getVerbosity')
            ->will($this->returnValue($verbosity));

        $condition = call_user_func([$this->windows, $method]);

        if ($expected) {
            $this->assertTrue($condition);
        } else {
            $this->assertFalse($condition);
        }
    }
}
