<?php

namespace App\Exception\VacationCalculator;

use DateTime;

/**
 * Class VacationRangeOutOfBoundsException
 * @package App\Exception\VacationCalculator
 */
class VacationRangeOutOfBoundsException extends \OutOfBoundsException implements ExceptionInterface
{
    /**
     * VacationRangeOutOfBoundsException constructor.
     * @param DateTime $vacationStart
     * @param DateTime $vacationEnd
     * @param DateTime $rangeStart
     * @param DateTime $rangeEnd
     */
    public function __construct(
        DateTime $vacationStart,
        DateTime $vacationEnd,
        DateTime $rangeStart,
        ?DateTime $rangeEnd
    ) {
        $message = sprintf(
            'Vacation date range is out of bounds. Expected date in range %s - %s, got vacation range %s - %s',
            $rangeStart->format('Y-m-d'),
            $rangeEnd ? $rangeEnd->format('Y-m-d') : '-',
            $vacationStart->format('Y-m-d'),
            $vacationEnd->format('Y-m-d')
        );
        parent::__construct($message, 0, null);
    }
}