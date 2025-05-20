<?php
namespace App\Factory;

use App\Entity\User;
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
            $this->em->getRepository(User::class)->createQueryBuilder('u'),
            $this->urlGenerator->generate('example')
        );

        $dg->addColumn('id', 'Id');
        $dg->addColumn('name', 'Name')
            ->setSearchable(true);
            
        $dg->addColumn('type', 'Type');

        return $dg;
    }
}