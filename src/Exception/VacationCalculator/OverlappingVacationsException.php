<?php

namespace App\Exception\VacationCalculator;

use DateTime;

/**
 * Class OverlappingVacationsException
 * @package App\Exception\VacationCalculator
 */
class OverlappingVacationsException extends \OutOfBoundsException implements ExceptionInterface
{
    /**
     * VacationRangeOutOfBoundsException constructor.
     * @param DateTime $vacationStart
     * @param DateTime $vacationEnd
     * @param DateTime $overlappingVacationStart
     * @param DateTime $overlappingVacationEnd
     */
    public function __construct(
        DateTime $vacationStart,
        DateTime $vacationEnd,
        DateTime $overlappingVacationStart,
        DateTime $overlappingVacationEnd
    ) {
        $message = sprintf(
            'Vacations are overlapping between themselves: %s - %s and %s - %s',
            $vacationStart->format('Y-m-d'),
            $vacationEnd->format('Y-m-d'),
            $overlappingVacationStart->format('Y-m-d'),
            $overlappingVacationEnd->format('Y-m-d')
        );
        parent::__construct($message, 0, null);
    }
}