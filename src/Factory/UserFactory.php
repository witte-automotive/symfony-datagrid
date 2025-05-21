<?php
namespace App\Factory;

use App\Entity\User;
use App\SyDataGrid\ActionTypeEnum;
use App\SyDataGrid\SyDataGrid;
use App\SyDataGrid\SyDataGridFactory;
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

        $dg->addAction(ActionTypeEnum::CREATE)
            ->setCallback(fn($row) => $this->urlGenerator->generate('test', ['id' => $row->getId()]));
        $dg->addAction(ActionTypeEnum::EDIT);
        $dg->addAction(ActionTypeEnum::SHOW);

        $dg->setSortable('position');

        return $dg;
    }
}