<?php


namespace AppBundle\Menu;

use AppBundle\Entity\UserAccounts\User;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class MenuBuilder
{

    private $factory;
   
    /**
     * @var Router
     */
    private $router;
    /**
     * @var TokenStorage
     */
    private $tokenStorage;
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param FactoryInterface $factory
     *
     * Add any other dependency you need
     * @param Router $router
     * @param TokenStorage $tokenStorage
     * @param RequestStack $requestStack
     */
    public function __construct(FactoryInterface $factory, Router $router,TokenStorage $tokenStorage,RequestStack $requestStack)
    {
        $this->factory = $factory;
        $this->router = $router;

        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
    }

    public function createMainMenu()
    {
        $menu = $this->factory->createItem('root');

        $user = $this->tokenStorage->getToken()->getUser();
        if($user instanceof User)
        {
            $roleNames = $user->getRoles();

            //Set the Parent Menu Root Item
            $root = $menu->getRoot();
            $root->setChildrenAttributes(array('id' => 'main-menu', 'class' => 'sm sm-oda'));

            if (in_array('ROLE_ADMINISTRATOR', $roleNames)) {
                $menu = $this->createAdminMenu($menu);
            }
        }

        return $menu;
    }

    public function createAdminMenu(ItemInterface $menu)
    {

        $menu->addChild('Home', array('route' => 'app_home_page', 'extras' => array('icon' => 'home')));
        $menu->getParent();

        $menu->addChild('Location', array('uri' => '#', 'extras' => array('icon' => 'map-marker')))
            ->addChild('Manage Regions', array('route' => 'region_list', 'extras' => $this->getCrudLinks('region')))->getParent()
            ->addChild('Manage Districts', array('route' => 'council_list', 'extras' => $this->getCrudLinks('topic')))->getParent()
            ->addChild('Manage Wards', array('route' => 'ward_list', 'extras' => $this->getCrudLinks('topic')))->getParent()
            ->setChildrenAttributes(['class'=>'dropdown'])
            ->getParent();

        $menu->addChild('Data', array('uri' => '#', 'extras' => array('icon' => 'file-text-o')))
            ->addChild('Manage Soil Type', array('route' => 'soil_type_list', 'extras' => $this->getCrudLinks('soil_type')))->getParent()
            ->setChildrenAttributes(['class'=>'dropdown'])
            ->getParent();


        $menu->addChild('Users', array('uri' => '#', 'extras' => array('icon' => 'users')))
            ->addChild('Manage Roles', array('route' => 'defined_roles_list', 'extras' => $this->getCrudLinks('defined_roles')))->getParent()
            ->addChild('Manage accounts', array('route' => 'portal_users_list', 'extras' => $this->getCrudLinks('portal_users')))->getParent()
            ->setChildrenAttributes(['class'=>'dropdown'])
            ->getParent();

        return $menu;
    }



    public function getParameter($name)
    {
        return $this->requestStack->getCurrentRequest()->get($name);
    }

    public function getCrudLinks($name)
    {
       return [
            'routes' => [
                        ['route' => $name.'_add'],
                        ['route' => $name.'_info'],
                        ['route' => $name.'_edit']
            ]
        ];

    }


}