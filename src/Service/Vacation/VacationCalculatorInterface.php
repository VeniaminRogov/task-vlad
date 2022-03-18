<?php

namespace App\Service\Vacation;

use App\Entity\Employee;
use App\Exception\VacationCalculator\ExceptionInterface;

/**
 * Interface VacationCalculatorInterface
 * @package App\Service\Vacation
 */
interface VacationCalculatorInterface
{
    const VACATION_DAYS_PER_YEAR = 20;

    /**
     * Calculates currently available (work) days, which can be used for vacation.
     *
     * @param Employee $employee
     *
     * @return int
     *
     * @throws ExceptionInterface
     */
    public function getAvailableVacationDays(Employee $employee): int;

    /**
     * Calculates all earned vacation days till today (doesn't matter used or not).
     *
     * @param Employee $employee
     *
     * @return int
     */
    public function getTotalEarnedVacationDays(Employee $employee): int;
}