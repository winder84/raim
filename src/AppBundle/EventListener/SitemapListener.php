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

        $exCategories = $this->em
            ->getRepository('AppBundle:ExternalCategory')
            ->findBy(array(
                'isActive' => 1
            ));
        foreach ($exCategories as $exCategory) {
            $urls[] = $this->router->generate('ex_category_route', array('id' => $exCategory->getId()), true);
        }
        $exCategories = null;

        $filterAliases = $this->em
            ->getRepository('AppBundle:FilterAlias')
            ->findAll();
        foreach ($filterAliases as $filterAlias) {
            $alias = $filterAlias->getAlias();
            if ($alias) {
                $urls[] = $this->router->generate('filter_route', array('alias' => $alias), true);
            }
        }
        $filterAliases = null;

//        $iterableResult = $this->em->createQuery("SELECT p FROM 'AppBundle\Entity\Product' p WHERE p.isDelete = 0")->iterate();
//        $i = 0;
//        while ((list($product) = $iterableResult->next()) !== false) {
//            $urls[] = $this->router->generate('product_detail_route', array('alias' => $product->getAlias()), true);
//            if ($i % 10000 == 0) {
//                $this->em->flush();
//                $this->em->clear('AppBundle\Entity\Product');
//            }
//            $i++;
//        }
//        $products = null;

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