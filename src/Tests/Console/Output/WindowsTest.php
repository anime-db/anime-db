<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Console\Output;

use AnimeDb\Bundle\AnimeDbBundle\Console\Output\Windows;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test Windows output
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Console\Output
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class WindowsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Console output
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $output;

    /**
     * Windows output
     *
     * @var \AnimeDb\Bundle\AnimeDbBundle\Console\Output\Windows
     */
    protected $windows;

    /**
     * mb_detect_order
     *
     * @var array
     */
    protected $charset = [];

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        if (!extension_loaded('mbstring')) {
            $this->markTestSkipped('Extension "mbstring" is not loaded');
        }

        parent::setUp();
        $this->charset = mb_detect_order();
        $this->output = $this->getMock('\Symfony\Component\Console\Output\ConsoleOutputInterface');
        $this->windows = new Windows($this->output);
    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    public function tearDown()
    {
        parent::tearDown();
        mb_detect_order($this->charset);
    }

    /**
     * Test change mb_detect_order
     */
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
     * Get data for write
     *
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
            ]
        ];
    }

    /**
     * Test write
     *
     * @dataProvider getDataForWrite
     *
     * @param string $messages
     * @param boolean $newline
     * @param integer $type
     * @param string $expected_messages
     * @param boolean $expected_newline
     * @param integer $expected_type
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
     * Get data for write line
     *
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
            ]
        ];
    }

    /**
     * Test write line
     *
     * @dataProvider getDataForWriteLn
     *
     * @param string $messages
     * @param string $type
     * @param string $expected_messages
     * @param string $expected_type
     */
    public function testWriteLn($messages, $type, $expected_messages, $expected_type) {
        $this->output
            ->expects($this->once())
            ->method('writeLn')
            ->with($expected_messages, $expected_type);
        $this->windows->writeLn($messages, $type);
    }

    /**
     * Test set verbosity
     */
    public function testSetVerbosity()
    {
        $this->output
            ->expects($this->once())
            ->method('setVerbosity')
            ->with(1);
        $this->windows->setVerbosity(1);
    }

    /**
     * Test get verbosity
     */
    public function testGetVerbosity()
    {
        $this->output
            ->expects($this->once())
            ->method('getVerbosity')
            ->willReturn(1);
        $this->assertEquals(1, $this->windows->getVerbosity());
    }

    /**
     * Test set decorated
     */
    public function testSetDecorated()
    {
        $this->output
            ->expects($this->once())
            ->method('setDecorated')
            ->with(true);
        $this->windows->setDecorated(true);
    }

    /**
     * Test is decorated
     */
    public function testIsDecorated()
    {
        $this->output
            ->expects($this->once())
            ->method('isDecorated')
            ->willReturn(true);
        $this->assertTrue($this->windows->isDecorated());
    }

    /**
     * Test set formatter
     */
    public function testSetFormatter()
    {
        $formatter = $this->getMock('\Symfony\Component\Console\Formatter\OutputFormatterInterface');
        $this->output
            ->expects($this->once())
            ->method('setFormatter')
            ->with($formatter);
        $this->windows->setFormatter($formatter);
    }

    /**
     * Test get formatter
     */
    public function testGetFormatter()
    {
        $formatter = $this->getMock('\Symfony\Component\Console\Formatter\OutputFormatterInterface');
        $this->output
            ->expects($this->once())
            ->method('getFormatter')
            ->willReturn($formatter);
        $this->assertEquals($formatter, $this->windows->getFormatter());
    }

    /**
     * Test set error output
     */
    public function testSetErrorOutput()
    {
        $output = $this->getMock('\Symfony\Component\Console\Output\OutputInterface');
        $this->output
            ->expects($this->once())
            ->method('setErrorOutput')
            ->with($output);
        $this->windows->setErrorOutput($output);
    }

    /**
     * Test get error output
     */
    public function testGetErrorOutput()
    {
        $output = $this->getMock('\Symfony\Component\Console\Output\OutputInterface');
        $this->output
            ->expects($this->once())
            ->method('getErrorOutput')
            ->willReturn($output);
        $this->assertEquals($output, $this->windows->getErrorOutput());
    }
}