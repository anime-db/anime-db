<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\CatalogBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Analog linux cron
 *
 * @package AnimeDB\CatalogBundle\Command
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Cron extends ContainerAwareCommand
{
    // Минимальные/максимальные значения для временных критериев
    const MINUTE_MIN = 0;
    const MINUTE_MAX = 59;
    const HOUR_MIN  = 0;
    const HOUR_MAX  = 23;
    const DAY_MIN  = 1;
    const DAY_MAX  = 31;
    const MONTH_MIN = 1;
    const MONTH_MAX = 12;
    const DOW_MIN  = 0;
    const DOW_MAX  = 7;

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::configure()
     */
    protected function configure()
    {
        $this->setName('animedb:cron')
            ->setDescription('Analog Cron');
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        // exit if disabled
        if (!$this->getContainer()->getParameter('cron')['enabled']) {
            return null;
        }

        $crontab = $this->getContainer()->getParameter('cron')['comands'];
        $crontab = $this->parse($crontab);

        if ($crontab) {
            while (true) {
                // TODO create an analog cron
                sleep(86400);
            }
        }
    }
    /**
     * Запуск задач из crontab'а
     */
    private function checkForRun($crontab)
    {
        // Максимальное количество дней в месяце и дней недели
        $max_days_count = self::DAY_MAX - self::DAY_MIN + 1;
        $max_dows_count = self::DOW_MAX - self::DOW_MIN + 1;

        // Получение текущего времени
        $current_date = getdate();

        // Перебор задач из crontab'а
        foreach ($crontab as $command) {
            // Проверка времени запуска задачи
            $need_run = false;
            $date = $command['time'];
            if(
                in_array($current_date['minutes'], $date['minutes']) &&
                in_array($current_date['hours'], $date['hours']) &&
                in_array($current_date['mon'], $date['months'])
            ) {
                // Дни месяца и дни недели не заданы как '*'
                if (
                    ($max_dows_count !== count($date['dows'])) &&
                    ($max_days_count !== count($date['days'])) &&
                    (
                        in_array($current_date['mday'], $date['days']) ||
                        in_array($current_date['wday'], $date['dows'])
                    )
                ) {
                    $need_run = true;

                    // Дни месяца или дни недели заданы как '*'
                } elseif(
                    in_array($current_date['mday'], $date['days']) &&
                    in_array($current_date['wday'], $date['dows'])
                ) {
                        $need_run = true;
                }

                if ($need_run) {
                    $this->runCommand($command['command']);
                }
            }
        }
    }

    /**
     * Run command
     *
     * @param string $command
     */
    private function runCommand($command)
    {
        exec(PHP_BINARY.' '.__DIR__.'/../../../../app/console '.$command.' &');
    }


    /**
     * Разбор crontab'а
     * @param array $crontab
     * @return array
     */
    private function parse(array $crontab)
    {
        // Шаблон для разбора строки crontab'а
        $pattern = '~^(?<minutes>[-0-9,/*]+)\s+(?<hours>[-0-9,/*]+)\s+' .
                '(?<days>[-0-9,/*]+)\s+' .
                '(?<months>[-0-9,/*]+|(-|Jan|Feb|Mar|Apr|May|Jul|Jun|Aug|Sep|Oct|Nov|Dec)+)\s+' .
                '(?<dows>[-0-9,/*]+|(-|Sun|Mon|Tue|Wen|Thu|Fri|Sat)+)\s*' .
                '(?<command>[^#]+)$~i';

        // Разбор строк crontab'а
        $parsed = array();
        foreach( $crontab as $command ) {
            if (preg_match($pattern, $command, $command) ) {

                // Замена имён месяцов их номерами
                $command['months'] = str_replace(
                        array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep', 'Oct','Nov','Dec'),
                        array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12),
                        $command['months']
                );

                // Замена названий дней недели их номерами
                $command['dows'] = str_replace(
                        array('Sun','Mon','Tue','Wed','Thu','Fri','Sat'),
                        array(0, 1, 2, 3, 4, 5, 6),
                        $command['dows']
                );

                // Преобразование времени запуска заданий в диапозон возможных значений
                $time['minutes'] = $this->convertRow($command['minutes'], self::MINUTE_MIN, self::MINUTE_MAX);
                $time['hours']   = $this->convertRow($command['hours'], self::HOUR_MIN, self::HOUR_MAX);
                $time['days']    = $this->convertRow($command['days'], self::DAY_MIN, self::DAY_MAX);
                $time['months']  = $this->convertRow($command['months'], self::MONTH_MIN, self::MONTH_MAX);
                $time['dows']    = $this->convertRow($command['dows'], self::DOW_MIN, self::DOW_MAX);

                $parsed[] = array('time' => $time, 'command' => trim($command['command']));
            }
        }
        return $parsed;
    }

    /**
     * Преобразование времени запуска задания из формата crontab'а
     * в список возможных значений
     *
     * @param string $row
     * @param int $min Минимальное значение для элемента
     * @param int $max Максимальное значение для элемента
     *
     * @return array Список возможных значений элемента
     */
    private function convertRow($row, $min, $max)
    {
        // Инициализация переменных
        $available = array();

        // Шаблон для разбора элемента
        $pattern = '~^((?<asterisk>\*)|((?<number>[0-9]{1,2})+(-(?<range>[0-9]{1,2}))?))(/(?<step>[0-9]{1,2}))?$~i';

        // Разделение списка элементов (1,2,3)
        $elements = explode(',', $row);

        // Получение возможных значений для каждого элемента
        foreach ($elements as $elements) {
            if (preg_match($pattern, $elements, $element)) {
                // Элемент равен "*"
                if (!empty($element['asterisk'])) {
                    $values = range($min, $max, (!empty($element['step'])) ? (int)$element['step'] : 1);
                    $available = array_merge($available, $values);

                    // Элемен - диапозон значений
                } elseif(!empty($element['range'])) {
                    $values = range(
                            $element['number'],
                            $element['range'],
                            (!empty( $element['step'])) ? (int)$element['step'] : 1
                    );
                    $available = array_merge( $available, $values );

                    // Элемент - обычное число
                } else {
                    $available = array_merge($available, array((int)$element['number']));
                }
            }
        }

        return $available;
    }
}