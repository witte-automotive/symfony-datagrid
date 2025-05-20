## Example usage

### User factory

```php
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
        $dg->addColumn('name', 'Name');
        $dg->addColumn('type', 'Type');

        return $dg;
    }
}
```

### Controller

```php
namespace App\Controller;

use App\Factory\UserFactory;
use App\SyDataGrid\SyDataGridFactory;
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
}
```

### Template

```twig
<twig:SyDataGrid grid="{{grid}}"/>
```
