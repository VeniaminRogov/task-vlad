<?php

namespace App\Service\Vacation;

use App\Entity\Employee;
use App\Entity\Vacation;
use App\Exception\VacationCalculator\OverlappingVacationsException;
use App\Exception\VacationCalculator\VacationRangeOutOfBoundsException;

class VacationCalculator implements VacationCalculatorInterface
{
    public function validateVacationRangeOfBounds(Employee $employee, Vacation $vacation)
    {
        $isValidRange = $employee->getHiredAt() >= $vacation->getStartDate() || $employee->getHiredAt() >= $vacation->getEndDate() ||
            ($employee->getDismissedAt() &&
                ($employee->getDismissedAt() <= $vacation->getStartDate() || $employee->getDismissedAt() <= $vacation->getEndDate()));
        if($isValidRange){
            throw new VacationRangeOutOfBoundsException(
                $vacation->getStartDate(),
                $vacation->getEndDate(),
                $employee->getHiredAt(),
                $employee->getDismissedAt()
            );
        }
    }

    public function validateOverlappingVacations(Vacation $vacation, Vacation $secondVacation)
    {
        if($vacation->getStartDate() < $secondVacation->getEndDate() && $vacation->getEndDate() > $secondVacation->getStartDate())
        {
            throw new OverlappingVacationsException(
                $vacation->getStartDate(),
                $vacation->getEndDate(),
                $secondVacation->getStartDate(),
                $secondVacation->getEndDate()
            );
        }
    }

    /**$overlappingVacationStart
     * @inheritDoc
     */
    public function getAvailableVacationDays(Employee $employee): int
    {
        $workDays = 0;
        $vacations = $employee->getVacations();
        foreach ($vacations as $vacation)
        {
            $this->validateVacationRangeOfBounds($employee, $vacation);
            foreach ($vacations as $secondVacation)
            {
                if ($vacation != $secondVacation)
                {
                    $this->validateOverlappingVacations($vacation, $secondVacation);
                }
            }

            $currentDate = clone $vacation->getStartDate();

            while ($currentDate < $vacation->getEndDate())
            {
                if($currentDate->format('w') >= 1 && $currentDate->format('w') <= 5)
                {
                    $workDays++;
                }
                $currentDate->modify('+1 day');
            }
        }

        return $this->getTotalEarnedVacationDays($employee) - $workDays;
//        throw new \LogicException('Not implemented. Go to ' . static::class . ' and implement required methods :)');
    }

    /**
     * @inheritDoc
     */
    public function getTotalEarnedVacationDays(Employee $employee): int
    {
        $days = $employee->getHiredAt()->diff($employee->getDismissedAt() ?  : new \DateTime())->days;
        return $days * self::VACATION_DAYS_PER_YEAR / 365;
    }
}