<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use App\Entity\Vacation;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Nubs\RandomNameGenerator\All;
use Nubs\RandomNameGenerator\Alliteration;
use Nubs\RandomNameGenerator\Generator;

class AppFixtures extends Fixture
{
    private const RANDOM_SEED = 2020;

    /** @var Generator|null */
    private $nameGenerator;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        srand(self::RANDOM_SEED);

        $companyFoundedAt = new DateTime('2012-01-01 midnight');

        $this->nameGenerator = new All([new Alliteration()]);

        $employees = [];

        $employees[] = $yearlyDirector = $this->createEmployee();
        $yearlyDirector->setHiredAt(clone $companyFoundedAt);

        $this->addVacations(
            $yearlyDirector,
            [$lastVacation = (clone $companyFoundedAt)->modify('+1 year')->modify('monday'), '+3 weeks'],
            [$lastVacation = (clone $lastVacation)->modify('+1 year'), '+3 weeks 5 days'],
            [$lastVacation = (clone $lastVacation)->modify('+1 year 2 months friday'), '+2 weeks 1 day'],
            [$lastVacation = (clone $lastVacation)->modify('+1 year'), '+4 weeks 6 days'],
            [$lastVacation = (clone $lastVacation)->modify('+1 year'), '+3 weeks'],
            [$lastVacation = (clone $lastVacation)->modify('+6 months'), '+4 weeks'],
            [$lastVacation = (clone $lastVacation)->modify('+1 year'), '+4 weeks 3 days'],
            [$lastVacation = (clone $lastVacation)->modify('+1 year'), '+4 weeks'],
            [$lastVacation = (clone $lastVacation)->modify('+5 months 1 day'), '+2 weeks 5 days']
        );

        $employees[] = $newProgrammer = $this->createEmployee();
        $newProgrammer->setHiredAt(new DateTime('-20 days midnight'));

        $employees[] = $firedManager = $this->createEmployee();
        $firedManager
            ->setHiredAt(new DateTime('2012-02-20 midnight'))
            ->setDismissedAt(new DateTime('2012-05-12 midnight'))
        ;

        $employees[] = $mondayManager = $this->createEmployee();
        $mondayManager->setHiredAt($hired = new DateTime('2018-06-06 midnight'));
        $mondayManager->setDismissedAt((clone $hired)->modify('1 year 6 months'));

        $this->addVacations(
            $mondayManager,
            [(clone $hired)->modify('6 months')->modify('monday'), '+3 weeks'],
            [(clone $hired)->modify('12 months')->modify('monday'), '+3 weeks']
        );

        $employees[] = $somebody = $this->createEmployee();
        $somebody
            ->setHiredAt($hired = new DateTime('2020-01-20 midnight'))
        ;

        $this->addVacations(
            $somebody,
            [new DateTime('2020-01-26 midnight'), new DateTime('2020-01-28 midnight')]
        );

        foreach ($employees as $employee) {
            $manager->persist($employee);
        }

        $manager->flush();
    }

    /**
     * @return Employee
     */
    private function createEmployee(): Employee
    {
        $employee = new Employee();
        $employee
            ->setFullName($this->nameGenerator->getName())
        ;

        return $employee;
    }

    /**
     * @param Employee $employee
     * @param array $vacationDatesArray
     */
    private function addVacations(Employee $employee, array ...$vacationDatesArray)
    {
        foreach ($vacationDatesArray as $vacationDates) {
            list($startDate, $modifyOrEndDate) = $vacationDates;

            if ($modifyOrEndDate instanceof DateTime) {
                $endDate = $modifyOrEndDate;
            } else {
                $endDate = (clone $startDate)->modify($modifyOrEndDate);
            }

            $vacation = new Vacation();
            $vacation
                ->setEmployee($employee)
                ->setStartDate($startDate)
                ->setEndDate($endDate)
            ;

            $employee->addVacation($vacation);
        }
    }
}
