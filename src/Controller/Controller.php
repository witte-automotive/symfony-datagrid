<?php
namespace App\Controller;

use App\Entity\User;
use App\SyDataGrid\Paginated;
use App\SyDataGrid\SyDataGrid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class Controller extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private UrlGeneratorInterface $urlGenerator, private Environment $twig)
    {
    }
    #[Route('/', name: 'example')]
    public function init(Request $request)
    {
        $dg = new SyDataGrid(
            $this->em->getRepository(User::class)->createQueryBuilder('u'),
            $this->urlGenerator->generate('example')
        );

        $dg->addColumn('id', 'Id');
        $dg->addColumn('name', 'Name');
        $dg->addColumn('type', 'Type');

        if ($request->isXmlHttpRequest()) {
            $dg->update($request);

            return $this->json([
                'pagination' => $dg->data->witnotData(),
                'html' => $this->twig->render('grid/grid.html.twig', [
                    'grid' => $dg
                ])
            ]);
        }

        return $this->render('index.html.twig', [
            'grid' => $dg
        ]);
    }


    #[Route('/widget/sydatagrid/update', methods: ['GET'], name: 'widget.sydatagrid.update')]
    public function update()
    {

    }
}