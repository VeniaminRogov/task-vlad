<?php

namespace App\Tests\Service\Vacation;

use App\Entity\Employee;
use App\Entity\Vacation;
use App\Exception\VacationCalculator\OverlappingVacationsException;
use App\Exception\VacationCalculator\VacationRangeOutOfBoundsException;
use App\Service\Vacation\VacationCalculator;
use App\Service\Vacation\VacationCalculatorInterface;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Class VacationCalculatorReferenceImplementation
 * @package App\Tests\Service\Vacation
 */
class VacationCalculatorTest extends TestCase
{
    private $vacationImplementationClassname = VacationCalculator::class;

    public function testCorrelationBetweenAvailableAndEarnedVacationDays()
    {
        $calculator = $this->getCalculator();
        $employee   = $this->getEmployee('25 months ago midnight', '3 months ago midnight');

        $employee
            // 15 work days, 4 months ago
            ->addVacation((new Vacation())
                ->setStartDate($start = (new DateTime('4 months ago'))->modify('monday midnight'))
                ->setEndDate((clone $start)->modify('+3 weeks'))
            );

        self::assertSame(
            15,
            $calculator->getTotalEarnedVacationDays($employee) - $calculator->getAvailableVacationDays($employee),
            'If user have 3 weeks vacation exactly, available vacations should differ on 15 days.'
        );

        // 16 work days, 10 months ago
        $employee->addVacation((new Vacation())
            ->setStartDate($start = (clone $start)->modify('6 months ago')->modify('friday'))
            ->setEndDate((clone $start)->modify('+3 weeks')->modify('+3 days'))
        );
//        dd($calculator->getTotalEarnedVacationDays($employee) .'-'. $calculator->getAvailableVacationDays($employee));
        self::assertSame(
            15 + 16,
            $calculator->getTotalEarnedVacationDays($employee) - $calculator->getAvailableVacationDays($employee),
            'Incorrect calculation for vacation if it is not perfectly aligned with week start and end.'
        );

        // 1 work day, 1 year and 2 months ago
        $employee->addVacation((new Vacation())
            ->setStartDate($start = (new DateTime('1 year 2 months ago'))->modify('monday midnight'))
            ->setEndDate((clone $start)->modify('1 day'))
        );

        self::assertSame(
            15 + 16 + 1,
            $calculator->getTotalEarnedVacationDays($employee) - $calculator->getAvailableVacationDays($employee),
            'Incorrect calculation for short, one day vacation'
        );

        // 1 work day, 1 month ago
        $employee->addVacation((new Vacation())
            ->setStartDate($start = (new DateTime('1 year 1 month ago'))->modify('friday midnight'))
            ->setEndDate((clone $start)->modify('1 day'))
        );

        self::assertSame(
            15 + 16 + 1 + 1,
            $calculator->getTotalEarnedVacationDays($employee) - $calculator->getAvailableVacationDays($employee),
            'Incorrect calculation for short vacation, finishing on holiday'
        );

        // 0 work day, 2 months ago
        $employee->addVacation((new Vacation())
            ->setStartDate($start = (new DateTime('7 months ago'))->modify('saturday midnight'))
            ->setEndDate((clone $start)->modify('1 day'))
        );

        self::assertSame(
            15 + 16 + 1 + 1,
            $calculator->getTotalEarnedVacationDays($employee) - $calculator->getAvailableVacationDays($employee),
            'Incorrect calculation for on-holiday vacation'
        );
    }

    public function testAvailableVacationDays()
    {
        $calculator = $this->getCalculator();

        $employee = $this->getEmployee('first day of january last year');

        $hiredAt = $employee->getHiredAt();

        $daysInYear = $hiredAt->diff((clone $hiredAt)->modify('+1 year'))->days;

        $halfYearDays = ceil($daysInYear / 2);

        $employee->setDismissedAt((clone $hiredAt)->add(new \DateInterval('P' . $halfYearDays . 'D')));

        self::assertSame(
            10,
            $calculator->getTotalEarnedVacationDays($employee),
            'If user worked exactly half of the year, he should have 10 vacation days earned.'
        );
    }

    public function testVacationRangeOutOfBoundsExceptionThrown()
    {
        $this->expectException(VacationRangeOutOfBoundsException::class);
        $employee = $this->getEmployee('2 years ago midnight', '1 year ago midnight');
        $employee->addVacation((new Vacation())
            ->setStartDate(new DateTime('3 years ago midnight'))
            ->setEndDate(new DateTime('25 months ago midnight')))
        ;

        $this->getCalculator()->getAvailableVacationDays($employee);
    }

    public function testOverlappingVacationExceptionThrown()
    {
        $this->expectException(OverlappingVacationsException::class);
        $employee = $this->getEmployee('4 years ago midnight', '1 year ago midnight');

        $employee
            ->addVacation((new Vacation())
                ->setStartDate(new DateTime('3 years ago midnight'))
                ->setEndDate(new DateTime('22 months ago midnight'))
            )
            ->addVacation((new Vacation())
                ->setStartDate(new DateTime('23 months ago midnight'))
                ->setEndDate(new DateTime('22 months ago'))
            )
        ;

        $this->getCalculator()->getAvailableVacationDays($employee);
    }

    /**
     * @param $hiredString
     * @param $dismissedString
     * @return Employee
     */
    private function getEmployee(string $hiredString, ?string $dismissedString = null): Employee
    {
        $employee = new Employee();
        $employee
            ->setFullName('Test User')
            ->setHiredAt(new DateTime($hiredString))
            ->setDismissedAt($dismissedString ? new DateTime($dismissedString) : null)
        ;

        return $employee;
    }

    /**
     * @return VacationCalculatorInterface
     */
    private function getCalculator(): VacationCalculatorInterface
    {
        return new $this->vacationImplementationClassname();
    }
}