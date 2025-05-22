<?php
namespace SyDataGrid\Controller;

use SyDataGrid\Factory\UserFactory;
use SyDataGrid\SyDataGrid\SyDataGridFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExampleController extends AbstractController
{
    public function __construct(
        private UserFactory $userFactory,
        private SyDataGridFactory $gridFactory
    ) {
    }

    #[Route('/', name: 'example')]
    public function init(Request $request)
    {
        $dg = $this->userFactory->createTable();

        if ($request->isXmlHttpRequest()) {
            return $this->json($this->gridFactory->refresh($dg, $request));
        }

        return $this->render('index.html.twig', [
            'grid' => $dg
        ]);
    }

    #[Route('/{id}', name: 'test')]
    public function test()
    {

    }
}