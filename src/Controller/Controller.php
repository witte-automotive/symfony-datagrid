<?php
namespace App\Controller;

use App\Entity\User;
use App\SyDataGrid\SyDataGrid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class Controller extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }
    #[Route('/')]
    public function init()
    {
        $dg = new SyDataGrid($this->em->getRepository(User::class)->createQueryBuilder('u'));

        $dg->addColumn('id', 'Id');
        $dg->addColumn('name', 'Name');
        $dg->addColumn('type', 'Type');

        dump($dg);

        $this->em->flush();
        return $this->render('grid/grid.html.twig', [
            'grid' => $dg
        ]);
    }


    #[Route('/widget/sydatagrid/update', methods: ['GET'], name: 'widget.sydatagrid.update')]
    public function update()
    {

    }
}