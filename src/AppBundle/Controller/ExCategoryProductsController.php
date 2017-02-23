<?php

namespace AppBundle\Controller;
use AppBundle\Entity\Category;
use AppBundle\Entity\ExternalCategory;
use Sonata\AdminBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;


class ExCategoryProductsController extends CoreController
{
    private $em;

    public function ExCategoryProductsAction(Request $request)
    {
        $externalCategoryId = $request->get('exCategoryId');
        $this->em = $this->getDoctrine()->getManager();
        /** @var ExternalCategory $externalCategory */
        $externalCategory = $this->em
            ->getRepository('AppBundle:ExternalCategory')
            ->findOneBy(array('id' => $externalCategoryId));
        $externalCategoryProducts = $this->em
            ->getRepository('AppBundle:Product')
            ->findBy(array('category' => $externalCategory), array(), 10);
        return $this->render('AppBundle:Admin:external.category.products.html.twig', array(
            'base_template' => $this->getBaseTemplate(),
            'admin_pool' => $this->container->get('sonata.admin.pool'),
            'blocks' => $this->container->getParameter('sonata.admin.configuration.dashboard_blocks'),
            'externalCategoryProducts' => $externalCategoryProducts,
        ));
    }
}