<?php
namespace SyDataGrid\Factory;

use SyDataGrid\Entity\User;
use SyDataGrid\SyDataGrid\SyDataGrid;
use SyDataGrid\SyDataGrid\SyDataGridFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserFactory
{
    public function __construct(
        private EntityManagerInterface $em,
        private UrlGeneratorInterface $urlGenerator,
        private SyDataGridFactory $gridFactory,
    ) {
    }
    public function createTable(): SyDataGrid
    {
        $dg = $this->gridFactory->create(
            $this->em->getRepository(User::class)->createQueryBuilder('u')->orderBy('u.position', 'asc'),
            $this->urlGenerator->generate('example')
        );

        $dg->setPrimaryKey('id');
        $dg->setDefaultDataSource('position', 'asc');

        $dg->addColumn('id', 'Id');
        $dg->addColumn('name', 'Name')
            ->setSearchable(true);

        $dg->addColumn('type', 'Type');
        $dg->addColumn('position', 'Position');


        $dg->setSortable('position');

        return $dg;
    }
}