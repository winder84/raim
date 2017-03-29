<?php

namespace AppBundle\Controller;
use AppBundle\Entity\Category;
use Sonata\AdminBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;


class EmptyCategoriesController extends CoreController
{
    private $em;

    public function emptyCategoriesAction(Request $request)
    {
        $this->em = $this->getDoctrine()->getManager();
        $isDisableAll = $request->request->get('disableAll');
        $categoriesToDisable = $request->request->get('categoriesToDisable');
        if ($isDisableAll && $categoriesToDisable) {
            foreach ($categoriesToDisable as $categoryToDisable) {
                /** @var Category $category */
                $category = $this->em
                    ->getRepository('AppBundle:Category')
                    ->findOneBy(array(
                        'id' => $categoryToDisable
                    ));
                $category->setIsActive(false);
                $this->em->persist($category);
                $this->em->flush();
            }
            return $this->redirect('/admin/emptyCategories');
        }
        $categories = $this->em
            ->getRepository('AppBundle:Category')
            ->findBy(array(
                'isActive' => 1
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
                ->having('cnt = 0')
                ->orderBy('cnt', 'DESC')
                ->setParameter('childCategories', $childExCategoryIds);
            $query = $qb->getQuery();
            $resultQuery = $query->getResult();
            if ($resultQuery) {
                $resultCategories[] = $category;
            }
        }
        return $this->render('AppBundle:Admin:empty.categories.html.twig', array(
            'base_template' => $this->getBaseTemplate(),
            'admin_pool' => $this->container->get('sonata.admin.pool'),
            'blocks' => $this->container->getParameter('sonata.admin.configuration.dashboard_blocks'),
            'resultCategories' => $resultCategories
        ));
    }
}