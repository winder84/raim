<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ExceptionController extends Controller
{
    private $metaTags;
    private $categories;
    private $sites;
    private $menuItems;

    /**
     * @Route("/404")
     * @Template()
     */
    public function show404Action()
    {
        $this->getMenuItems();
        $this->getMetaItems();
        $this->metaTags['metaRobots'] = 'NOINDEX, NOFOLLOW';
        return $this->render('AppBundle:Exception:show404.html.twig', array(
            'menuItems' => $this->menuItems,
            'metaTags' => $this->metaTags,
        ));
    }

    private function getMenuItems()
    {
        $em = $this->getDoctrine()->getManager();
        $this->menuItems['categories'] = $em
            ->getRepository('AppBundle:Category')
            ->findAll();

        $this->menuItems['sites'] = $em
            ->getRepository('AppBundle:Site')
            ->findAll();
        $qb = $em->createQueryBuilder();

        $qb->select('Vendor.alias, Vendor.name, count(p.id) as cnt')
            ->from('AppBundle:Vendor', 'Vendor')
            ->leftJoin('Vendor.products', 'p')
            ->having('cnt > 450')
            ->groupBy('Vendor.alias')
            ->orderBy('cnt', 'DESC')
            ->setMaxResults(25);
        $query = $qb->getQuery();
        $resultVendors = $query->getResult();
        foreach ($resultVendors as $resultVendor) {
            $this->menuItems['vendors'][] = $resultVendor;
        }
        $this->menuItems['slideUrl'] = '/bundles/app/images/slBg1.png';
        $this->menuItems['slideText'] = 'Современная одежда для Вашей семьи';
    }

    private function getMetaItems()
    {
        $this->metaTags['metaTitle'] = 'Всё для вашего ребенка!';
        $this->metaTags['metaDescription'] = 'У нас Вы найдете всё самое лучшее для Вашего ребенка!';
        $this->metaTags['metaKeywords'] = 'ребенок, дети, детё, сын, дочь, игрушки, книжки, кроватки, детская еда';
        $this->metaTags['metaRobots'] = 'all';
    }
}
