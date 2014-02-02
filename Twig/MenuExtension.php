<?php
namespace DM\MenuBundle\Twig;

use DM\MenuBundle\Menu\MenuFactoryInterface;
use DM\MenuBundle\MenuConfig\MenuConfigProvider;

class MenuExtension extends \Twig_Extension {

    /**
     * @var MenuFactoryInterface
     */
    private $menuFactory;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var MenuConfigProvider
     */
    protected $menuConfigProvider;

    public function __construct(
        MenuFactoryInterface $menuFactory, \Twig_Environment $twig,
        MenuConfigProvider $menuConfigProvider
    ){
        $this->menuFactory = $menuFactory;
        $this->twig = $twig;
        $this->menuConfigProvider = $menuConfigProvider;
    }

    public function getFunctions()
    {
        return array(
            'dm_menu_render' => new \Twig_Function_Method($this, 'render', array('is_safe' => array('html')))
        );
    }

    /**
     * @param $name
     * @param array $options
     * @return mixed
     */
    public function render($name, array $options = array())
    {
        $menu = $this->menuFactory->create($name);

        $defaultOptions = array(
            'collapse' => false,
            'nested' => true
        );
        
        $finalOptions = array_merge($defaultOptions, $options);
        $finalOptions['currentNode'] = $menu;

        return $this->getTemplate($name)->renderBlock('render_root', $finalOptions);
    }

    /**
     * @param $name
     * @return \Twig_TemplateInterface
     */
    protected function getTemplate($name)
    {
        $menuConfig = $this->menuConfigProvider->getMenuConfig($name);

        return $this->twig->loadTemplate($menuConfig['twig_template']);
    }

    public function getName()
    {
        return 'dm_menu_extension';
    }
} 