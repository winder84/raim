<?php
namespace AppBundle\EventListener;

use Symfony\Component\Routing\RouterInterface;

use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Doctrine\ORM\EntityManager;

class SitemapListener implements SitemapListenerInterface
{
    private $router;
    protected $em;

    public function __construct(RouterInterface $router, EntityManager $em)
    {
        $this->em = $em;
        $this->router = $router;
    }

    public function populateSitemap(SitemapPopulateEvent $event)
    {
        $section = $event->getSection();
        if (is_null($section) || $section == 'default') {
            //get absolute homepage url
            $urls[] = $this->router->generate('homepage', array(), true);
        }

        $sites = $this->em->getRepository('AppBundle:Site')->findAll();
        foreach ($sites as $site) {
            $urls[] = $this->router->generate('shop_description_route', array('alias' => $site->getAlias()), true);
        }
        $sites = null;

        $vendors = $this->em
            ->getRepository('AppBundle:Vendor')
            ->findBy(array(
                'isActive' => 1
            ));
        foreach ($vendors as $vendor) {
            $urls[] = $this->router->generate('vendor_route', array('alias' => $vendor->getAlias()), true);
        }
        $vendors= null;

        $exCategories = $this->em
            ->getRepository('AppBundle:ExternalCategory')
            ->findBy(array(
                'isActive' => 1
            ));
        foreach ($exCategories as $exCategory) {
            $urls[] = $this->router->generate('ex_category_route', array('id' => $exCategory->getId()), true);
        }
        $exCategories = null;

        $categories = $this->em
            ->getRepository('AppBundle:Category')
            ->findBy(array(
                'isActive' => 1
            ));
        foreach ($categories as $category) {
            $urls[] = $this->router->generate('category_route', array('alias' => $category->getAlias()), true);
        }
        $categories = null;

        $filterPages = array();
        $iterableResult = $this->em->createQuery("SELECT p FROM 'AppBundle\Entity\Product' p WHERE p.isDelete = 0")->iterate();
        $i = 0;
        while ((list($product) = $iterableResult->next()) !== false) {
            $urls[] = $this->router->generate('product_detail_route', array('alias' => $product->getAlias()), true);
            $vendor = $product->getVendor();
            $exCategory = $product->getCategory();
            if ($vendor && $exCategory && $vendor->getIsActive() && $exCategory->getIsActive()) {
                $path = $this->router->generate('filter_route', array(
                    'vendorAlias' => mb_strtolower($vendor->getAlias(), 'UTF-8'),
                    'categoryId' => $exCategory->getId(),
                ), true);
                $filterPages[$path] = $path;
            }
            if ($i % 10000 == 0) {
                $this->em->flush();
                $this->em->clear('AppBundle\Entity\Product');
            }
            $vendor = null;
            $exCategory = null;
            $i++;
        }
        $urls = array_merge($urls, array_values($filterPages));
        $products = null;

        foreach ($urls as $url) {
            $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $url,
                    new \DateTime(),
                    UrlConcrete::CHANGEFREQ_WEEKLY,
                    0.7
                ),
                'default'
            );
        }
    }
}