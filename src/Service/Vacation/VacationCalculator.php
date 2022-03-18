<?php

namespace App\Service\Vacation;

use App\Entity\Employee;

class VacationCalculator implements VacationCalculatorInterface
{
    /**
     * @inheritDoc
     */
    public function getAvailableVacationDays(Employee $employee): int
    {
        throw new \LogicException('Not implemented. Go to ' . static::class . ' and implement required methods :)');
    }

    /**
     * @inheritDoc
     */
    public function getTotalEarnedVacationDays(Employee $employee): int
    {
        throw new \LogicException('Not implemented. Go to ' . static::class . ' and implement required methods :)');
    }
}