<?php
namespace Witte\SyDatagrid\ExampleFactory;

use Witte\SyDatagrid\DataGrid\SyDataGrid;
use Witte\SyDatagrid\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Witte\SyDatagrid\Enum\ActionTypeEnum;
use Witte\SyDatagrid\Enum\SearchableColumnEnum;
use Witte\SyDatagrid\Factory\SyDataGridFactory;

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
        // $dg->setDefaultOrder('position', 'asc');

        $dg->addColumn('id', 'Id')
            ->setSearchable();

        $dg->addColumn('name', 'Name')
            ->setSearchable();

        $dg->addColumn('type', 'Type')
            ->setSearchable(SearchableColumnEnum::OPTIONS_SELECT, [
                'options' => [
                    'a' => 'Coby',
                    'b' => 'Alphonso'
                ]
            ]);

        $dg->addColumn('position', 'Position');

        $dg->addAction(ActionTypeEnum::SHOW);

        $dg->setSortable('position');

        return $dg;
    }
}