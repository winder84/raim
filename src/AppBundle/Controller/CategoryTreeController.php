<?php

namespace AppBundle\Controller;
use AppBundle\Entity\Category;
use Sonata\AdminBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;


class CategoryTreeController extends CoreController
{
    private $em;

    public function categoryTreeAction(Request $request)
    {
        $categoryTreeArray = array();
        $this->em = $this->getDoctrine()->getManager();
        $categories = $this->em
            ->getRepository('AppBundle:Category')
            ->findBy(array(
                'isActive' => 1,
            ));
        /** @var Category $category */
        foreach ($categories as $category) {
            if (!$category->getParent()) {
                $categoryTreeArray[$category->getId()] = $category;
            }
        }
        return $this->render('AppBundle:Admin:category.tree.html.twig', array(
            'base_template' => $this->getBaseTemplate(),
            'admin_pool' => $this->container->get('sonata.admin.pool'),
            'blocks' => $this->container->getParameter('sonata.admin.configuration.dashboard_blocks'),
            'categoryTreeArray' => $categoryTreeArray,
        ));
    }
}