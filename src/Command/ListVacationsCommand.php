<?php

namespace App\Command;

use App\Entity\Employee;
use App\Service\Vacation\VacationCalculatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ListVacationsCommand
 * @package App\Command
 */
class ListVacationsCommand extends Command
{
    protected static $defaultName = 'app:vacations:list';

    /**
     * @var VacationCalculatorInterface
     */
    private $vacationCalculator;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ListVacationsCommand constructor.
     * @param EntityManagerInterface $em
     * @param VacationCalculatorInterface $vacationCalculator
     */
    public function __construct(EntityManagerInterface $em, ?VacationCalculatorInterface $vacationCalculator = null)
    {
        parent::__construct(self::$defaultName);
        $this->vacationCalculator = $vacationCalculator;
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);

        /** @var Employee[] $employees */
        $employees = $this->em->getRepository(Employee::class)->findAll();

        $tableRows = [];

        foreach ($employees as $employee) {
            $tableRows[] =
                [
                    $employee->getFullName(),
                    $employee->getHiredAt()->format('d.m.Y'),
                    $employee->getDismissedAt() ? $employee->getDismissedAt()->format('d.m.Y') : 'Working until now',
                    $earnedVacations = $this->vacationCalculator->getTotalEarnedVacationDays($employee),
                    $earnedVacations - ($availableVacations = $this->vacationCalculator->getAvailableVacationDays($employee)),
                    $availableVacations,
                ];
        }

        $style->table(['Name', 'Hired', 'Dismissed', 'Earned vacations', 'Spent vacations', 'Available vacations'], $tableRows);

        return 0;
    }
}