<?php

namespace Treinabo\Console\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputOption;
use Treinabo\CarbonTreinabo as Carbon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TrainCommand extends Command
{
    /**
     * Starting day of the calculation
     *
     * @var Carbon
     */
    protected $start_date;

    /**
     * Number of extra days to show
     *
     * @var int
     */
    private $plus_days;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('train')
            ->setDescription('Lists best dates to buy the train abo')
            ->addArgument(
                'start-date',
                InputArgument::OPTIONAL,
                'What date to start checking?',
                date('Y-m-d')
            )
            ->addOption(
                'extra-days',
                'e',
                InputOption::VALUE_OPTIONAL,
                'How many extra days to check?',
                14
            )
            ->addOption(
                'holidays',
                'd',
                InputOption::VALUE_OPTIONAL,
                'CSV file with holidays'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->start_date = Carbon::parse($input->getArgument('start-date'));
        $this->plus_days = $input->getOption('extra-days');

        $holidays = $input->getOption('holidays');
        if ($holidays) {
            Carbon::setHolidaysCsv($holidays);
        }

        $table = new Table($output);
        $table->setHeaders(array('Start date', 'Number of working days'))
            ->setRows($this->calculateNumDays())
            ->render();
    }

    /**
     * Gets the working days dates.
     *
     * @throws \Exception
     * @return array
     */
    protected function getWorkingDays()
    {
        $dates = [];
        $end_date = clone $this->start_date;
        $end_date->addDays($this->plus_days)->addMonth();
        for ($date = $this->start_date->copy(); $date->lte($end_date); $date->addDay()) {
            if (!$date->isWorkday()) {
                continue;
            }

            if ($date->isHoliday()) {
                continue;
            }

            $dates[$date->format('Y-m-d')] = $date->copy();
        }

        if (empty($dates)) {
            throw new \Exception('No dates found?', 2);
        }

        return $dates;
    }

    /**
     * Calculates the number of working days starting from the first few days.
     *
     * @return array
     */
    protected function calculateNumDays()
    {
        $dates = $this->getWorkingDays();

        $num_days = [];
        $last_check_date = $this->start_date->addDays($this->plus_days);
        for ($i = 0; $i < $this->plus_days; $i++) {
            /** @var Carbon $first_date */
            $first_date = reset($dates);
            if ($first_date->gt($last_check_date)) {
                break;
            }
            $last_date = clone $first_date;
            $last_date->addMonth();

            $num = array_reduce($dates, function ($sum, $day) use ($last_date) {
                /** @var Carbon $day */
                if ($day->lte($last_date)) {
                    $sum += 1;
                }

                return $sum;
            });

            // Drop first day
            array_shift($dates);

            $num_days[] = [$first_date->format('Y-m-d'), $num];
        }

        if (empty($num_days)) {
            throw new \Exception('No dates to show?', 3);
        }

        return $num_days;
    }
}
