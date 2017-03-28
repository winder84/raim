<?php

namespace AppBundle\Controller;
use AppBundle\Entity\Category;
use Sonata\AdminBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;


class NotActiveCategoriesController extends CoreController
{
    private $em;

    public function NotActiveCategoriesAction(Request $request)
    {
        $this->em = $this->getDoctrine()->getManager();
        $categories = $this->em
            ->getRepository('AppBundle:Category')
            ->findBy(array(
                'isActive' => 0
            ));
        $resultCategories = array();
        $defaultController = new DefaultController();
        /** @var Category $category */
        foreach ($categories as $category) {
            $childExCategoryIds = $defaultController->getChildCategoryIds($category);
            $qb = $this->em->createQueryBuilder();
            $qb->select('exCategory.id, exCategory.name, count(Product.id) as cnt')
                ->from('AppBundle:ExternalCategory', 'exCategory')
                ->leftJoin('exCategory.products', 'Product')
                ->where('exCategory IN (:childCategories)')
                ->andWhere('exCategory.isActive = 1')
                ->having('cnt > 0')
                ->orderBy('cnt', 'DESC')
                ->setParameter('childCategories', $childExCategoryIds);
            $query = $qb->getQuery();
            $resultQuery = $query->getResult();
            if ($resultQuery) {
                $resultCategories[] = $category;
            }
        }
        return $this->render('AppBundle:Admin:not.active.categories.html.twig', array(
            'base_template' => $this->getBaseTemplate(),
            'admin_pool' => $this->container->get('sonata.admin.pool'),
            'blocks' => $this->container->getParameter('sonata.admin.configuration.dashboard_blocks'),
            'resultCategories' => $resultCategories
        ));
    }
}