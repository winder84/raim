<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\ExternalCategory;
use AppBundle\Entity\FilterAlias;
use AppBundle\Entity\Product;
use AppBundle\Entity\Stat;
use AppBundle\Entity\Vendor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    private $menuItems = array();
    private $metaTags = array();
    private $productsPerPage = 20;
    private $breadcrumbsCategories = array();
    private $chooseProductsCount = 12;

    public function __construct()
    {
        $this->getMetaItems();
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $resultProducts = array();
        $notNeedArray = array(0);
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('Stat.productId, COUNT(Stat.productId) AS cnt')
            ->from('AppBundle:Stat', 'Stat')
            ->groupBy('Stat.productId')
            ->orderBy('cnt', 'DESC')
            ->setMaxResults($this->chooseProductsCount);
        $query = $qb->getQuery();
        $productsStats = $query->getResult();
        $productsIds = array();
        foreach ($productsStats as $productsStat) {
            $productsIds[] = $productsStat['productId'];
        }
        $products = $em
            ->getRepository('AppBundle:Product')
            ->findBy(
                array(
                    'id' => $productsIds,
                    'isDelete' => false
                ),
                array(),
                $this->chooseProductsCount
            );
        if (count($products) < $this->chooseProductsCount) {
            $qb = $em->createQueryBuilder();
            foreach ($products as $product) {
                $notNeedArray[] = $product->getId();
            }
            $needCount = $this->chooseProductsCount - count($products);
            $qb->select('Product')
                ->from('AppBundle:Product', 'Product')
                ->where('Product.id NOT IN (:notNeedArray)')
                ->andWhere('Product.isDelete = 0')
                ->setParameter('notNeedArray', $notNeedArray)
                ->setMaxResults($needCount);
            $query = $qb->getQuery();
            $moreProducts = $query->getResult();
            $products = array_merge($products, $moreProducts);
        }
        /** @var Product $product */
        foreach ($products as $product) {
            $resultProducts[] = array(
                'name' => $product->getName(),
                'model' => $product->getModel(),
                'pictures' => $product->getPictures(),
                'id' => $product->getId(),
                'url' => $product->getUrl(),
                'price' => $product->getPrice(),
                'alias' => $product->getAlias(),
                'oldPrice' => $product->getOldPrice(),
            );
        }

        $this->getMenuItems();
        return $this->render('AppBundle:Default:index.html.twig', array(
            'products' => $resultProducts,
            'vendors' => $this->menuItems['vendors'],
            'menuItems' => $this->menuItems,
            'metaTags' => $this->metaTags,
            'paginatorData' => null,
        ));
    }

    /**
     * @Route("/shop/description/{alias}", name="shop_description_route")
     */
    public function siteDescriptionAction($alias)
    {
        $em = $this->getDoctrine()->getManager();
        $site = $em
            ->getRepository('AppBundle:Site')
            ->findOneBy(array('alias' => $alias));
        $this->metaTags['metaTitle'] = 'Описание магазина ' . $site->getTitle() . '. Купить товары "' . $site->getTitle() . '" с доставкой по России.';

        $qb = $em->createQueryBuilder();
        $qb->select('Vendor.name, Vendor.alias, count(p.id) as cnt')
            ->from('AppBundle:Vendor', 'Vendor')
            ->leftJoin('Vendor.products', 'p')
            ->where('Vendor.isActive = 1')
            ->andWhere('Vendor.site = :site')
            ->groupBy('Vendor.id')
            ->orderBy('cnt', 'DESC')
            ->setParameter('site', $site)
            ->setMaxResults(12);
        $query = $qb->getQuery();
        $vendors = $query->getResult();

        $this->getMenuItems();
        return $this->render('AppBundle:Default:site.description.html.twig', array(
                'site' => $site,
                'metaTags' => $this->metaTags,
                'menuItems' => $this->menuItems,
                'vendors' => $vendors
            )
        );
    }

    /**
     * @Route("/shop/{alias}/{page}", name="shop_route")
     */
    public function siteAction($alias, $page = 1)
    {
        $this->metaTags['metaRobots'] = 'noindex';
        $em = $this->getDoctrine()->getManager();
        $site = $em
            ->getRepository('AppBundle:Site')
            ->findOneBy(array('alias' => $alias));

        $qb = $em->createQueryBuilder();
        $qb->select('Vendor.name, Vendor.alias, count(p.id) as cnt')
            ->from('AppBundle:Vendor', 'Vendor')
            ->leftJoin('Vendor.products', 'p')
            ->where('Vendor.isActive = 1')
            ->andWhere('Vendor.site = :site')
            ->groupBy('Vendor.id')
            ->orderBy('cnt', 'DESC')
            ->setParameter('site', $site)
            ->setMaxResults(12);
        $query = $qb->getQuery();
        $vendors = $query->getResult();

        $qb = $em->createQueryBuilder();
        $qb->select('Product')
            ->from('AppBundle:Product', 'Product')
            ->where('Product.site = :site')
            ->andWhere('Product.isDelete = 0')
            ->setParameter('site', $site);
        $query = $qb->getQuery()
            ->setFirstResult($this->productsPerPage * ($page - 1))
            ->setMaxResults($this->productsPerPage);
        $products = new Paginator($query, $fetchJoinCollection = true);

        $productsCount = count($products);
        $paginatorPagesCount = ceil($productsCount / $this->productsPerPage);
        $path = "/shop/$alias/";
        if ($productsCount <= $this->productsPerPage) {
            $paginatorData = null;
        } else {
            $paginatorData = $this->getPaginatorData($paginatorPagesCount, $page, 1, 5, $path);
        }

        $this->getMenuItems();
        return $this->render('AppBundle:Default:site.html.twig', array(
                'site' => $site,
                'metaTags' => $this->metaTags,
                'menuItems' => $this->menuItems,
                'products' => $products,
                'paginatorData' => $paginatorData,
                'vendors' => $vendors
            )
        );
    }

//    /**
//     * @Route("/exCategory/{id}/{page}", name="ex_category_route")
//     */
//    public function exCategoryAction($id, $page = 1)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $exCategory = $em
//            ->getRepository('AppBundle:ExternalCategory')
//            ->findOneBy(array(
//                'id' => $id,
//                'isActive' => 1
//            ));
//        if (!$exCategory) {
//            throw $this->createNotFoundException();
//        }
//        $this->metaTags['metaTitle'] = 'Купить ' . mb_strtolower($exCategory->getName(), 'UTF-8') . ' с доставкой по России.';
//        $parentId = $exCategory->getParentId();
//        $parentCategory = $em
//            ->getRepository('AppBundle:ExternalCategory')
//            ->findOneBy(array(
//                'externalId' => $parentId,
//                'isActive' => 1
//            ));
//        if ($parentCategory) {
//            $internalParentCategory = $parentCategory->getInternalParentCategory();
//        }
//        $childCategoriesIds = $this->getChildCategoryIds($exCategory);
//        $childCategoriesIds[] = $exCategory->getId();
//        $qb = $em->createQueryBuilder();
//        $qb->select('Product')
//            ->from('AppBundle:Product', 'Product')
//            ->where('Product.category IN (:childCategoriesIds)')
//            ->andWhere('Product.isDelete = 0')
//            ->setParameter('childCategoriesIds', $childCategoriesIds);
//        $query = $qb->getQuery()
//            ->setFirstResult($this->productsPerPage * ($page - 1))
//            ->setMaxResults($this->productsPerPage);
//        $products = new Paginator($query, $fetchJoinCollection = true);
//
//        $productsCount = count($products);
//        $paginatorPagesCount = ceil($productsCount / $this->productsPerPage);
//        $path = "/exCategory/$id/";
//        if ($productsCount <= $this->productsPerPage) {
//            $paginatorData = null;
//        } else {
//            $paginatorData = $this->getPaginatorData($paginatorPagesCount, $page, 1, 5, $path);
//        }
//
//        $this->getMenuItems();
//        $returnArray = array(
//            'products' => $products,
//            'paginatorData' => $paginatorData,
//            'exCategory' => $exCategory,
//            'metaTags' => $this->metaTags
//        );
//        if (isset($internalParentCategory)) {
//            $media = $internalParentCategory->getMedia();
//            if ($media) {
//                $provider = $this->container->get($media->getProviderName());
//                $url = $provider->generatePublicUrl($media, 'reference');
//                $this->menuItems['slideUrl'] = $url;
//            }
//            if (!empty($internalParentCategory->getSeoDescription())) {
//                $this->menuItems['slideText'] = $internalParentCategory->getSeoDescription();
//            }
//            if (!empty($parentCategory)) {
//                $exCategories = $em
//                    ->getRepository('AppBundle:ExternalCategory')
//                    ->findBy(array('parentId' => $parentCategory->getExternalId()));
//                if ($exCategories) {
//                    $returnArray['exCategories'] = $exCategories;
//                }
//            }
//        }
//        $returnArray['menuItems'] = $this->menuItems;
//        return $this->render('AppBundle:Default:exCategory.html.twig', $returnArray);
//    }

    /**
     * @Route("/product/detail/{alias}", name="product_detail_route")
     */
    public function productDetailAction($alias)
    {
        $likeProducts = array();
        $categoryProducts = array();
        $productGroupAlias = array();
        $em = $this->getDoctrine()->getManager();
        $product = $em
            ->getRepository('AppBundle:Product')
            ->findOneBy(array('alias' => $alias));
        if (!$product) {
            throw $this->createNotFoundException('The product does not exist');
        }
        if ($product->getIsDelete()) {
            $this->metaTags['metaRobots'] = 'noindex, nofollow';
        }
        $this->checkAndInsertFilterAliasByProduct($product);
        $productCategory = $product->getCategory();
        $productCategoryName = '';
        $categoryAlias = '';
        if ($productCategory) {
            $categoryAlias = 'category+' . $productCategory->getInternalParentCategory()->getAlias();
            $productCategoryName = $productCategory->getName();
            $categoryProducts = $productCategory->getProducts();
        }
        $productVendor = $product->getVendor();
        $productVendorName = '';
        if ($productVendor) {
            $productVendorName = $productVendor->getName();
        }
        if ($productVendor && $productCategory) {
            $productGroupAlias = array(
                'alias' => 'category+' . $productCategory->getInternalParentCategory()->getAlias() . '__vendor+' . $productVendor->getAlias(),
                'name' => $productCategory->getInternalParentCategory()->getName() . ' ' . $productVendor->getName(),
            );
        }
        foreach ($categoryProducts as $categoryProduct) {
            if (count($likeProducts) < 4) {
                if ($categoryProduct->getId() != $product->getId() && !$categoryProduct->getIsDelete()) {
                    $likeProducts[] = $categoryProduct;
                }
            }
        }

        if (!empty($productCategoryName)) {
            $productKeywords[] =  $productCategoryName . ' купить';
            $productFullName[] = $productCategoryName;
        }
        if (!empty($productVendorName)) {
            $productKeywords[] =  $productVendorName . ' купить';
            $productFullName[] = $productVendorName;
        }
        $productFullName[] = $product->getModel();
        $productFullName = array_filter($productFullName);
        $this->getMenuItems();
        $this->metaTags['metaTitle'] = 'Описание и цена ' . mb_strtolower($product->getName(), 'UTF-8') . '. Купить ' . mb_strtolower(implode(' | ', $productFullName), 'UTF-8') . ' с доставкой по России.';
        $this->metaTags['metaDescription'] = substr($product->getDescription(), 0, 400);
        $productKeywords[] =  $product->getName() . ' ' . $product->getModel() . ' купить';
        $this->metaTags['metaKeywords'] .= ',' . implode(',', $productKeywords);
        $this->getBreadcrumbs($product, 'product');
        $breadcrumbsCategories = array_reverse($this->breadcrumbsCategories);
        return $this->render('AppBundle:Default:product.description.html.twig', array(
                'product' => $product,
                'metaTags' => $this->metaTags,
                'likeProducts' => $likeProducts,
                'paginatorData' => null,
                'menuItems' => $this->menuItems,
                'breadcrumbsCategories' => $breadcrumbsCategories,
                'productGroupAlias' => $productGroupAlias,
                'categoryAlias' => $categoryAlias,
            )
        );
    }

    /**
     * @Route("/filter/{alias}/{page}", name="filter_route")
     * @param $alias
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function filterAction($alias, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        if ($alias) {
            $alias = str_replace(' ', '+', $alias);
        }
        $parseAliasLevelOne = explode('__', $alias);
        $parseAliasLevelTwo = array();
        if (!$parseAliasLevelOne) {
            throw $this->createNotFoundException('Alias does not found');
        }
        foreach ($parseAliasLevelOne as $parseAliasItem) {
            list($key, $value) = explode('+', $parseAliasItem);
            $parseAliasLevelTwo[] = array(
                'name' => $key,
                'alias' => $value,
            );
        }
        if (!$parseAliasLevelTwo) {
            throw $this->createNotFoundException('Alias does not parsed');
        }
        $filterAlias = $em
            ->getRepository('AppBundle:FilterAlias')
            ->findOneBy(array(
                'alias' => $alias
            ));
        $qb = $em->createQueryBuilder();
        $qb->select('count(Product.id) AS cnt')
            ->from('AppBundle:Product', 'Product')
            ->where('Product.isDelete = 0');
        $qb = $this->getQbByAlias($parseAliasLevelTwo, $qb);
        $allProducts = $qb->getQuery()->getResult();
        $productsCount = $allProducts[0]['cnt'];

        $qb = $em->createQueryBuilder();
        $qb->select('Product')
            ->from('AppBundle:Product', 'Product')
            ->where('Product.isDelete = 0');
        $qb = $this->getQbByAlias($parseAliasLevelTwo, $qb);
        $query = $qb->getQuery();
        $products = $query
            ->setFirstResult($this->productsPerPage * ($page - 1))
            ->setMaxResults($this->productsPerPage)
            ->getResult();
        $paginatorPagesCount = ceil($productsCount / $this->productsPerPage);
        $path = "/filter/{$alias}/";
        if ($productsCount <= $this->productsPerPage) {
            $paginatorData = null;
        } else {
            $paginatorData = $this->getPaginatorData($paginatorPagesCount, $page, 1, 5, $path);
        }
        $this->getMenuItems();
        if ($filterAlias) {
            $this->metaTags['metaTitle'] = 'Купить ' . $filterAlias->getAliasText() . ' со скидкой';;
            $this->metaTags['metaDescription'] = 'купить ' . $filterAlias->getAliasText() . ' с доставкой';
        }
        $breadcrumbsCategories = array();
        $categoryChildren = null;
        foreach ($parseAliasLevelTwo as $parseItem) {
            if ($parseItem['name'] == 'category') {
                /** @var Category $category */
                $category = $em
                    ->getRepository('AppBundle:Category')
                    ->findOneBy(array(
                        'alias' => $parseItem['alias']
                    ));
                $this->getBreadcrumbs($category, 'category');
                $breadcrumbsCategories = array_reverse($this->breadcrumbsCategories);
                $categoryChildren = $category->getChildren();
                if (!$filterAlias) {
                    $this->metaTags['metaTitle'] = 'Купить ' . $category->getName() . ' со скидкой';;
                    $this->metaTags['metaDescription'] = 'купить ' . $category->getName() . ' с доставкой';
                }
                break;
            }
        }
        $returnArray = array(
            'metaTags' => $this->metaTags,
            'paginatorData' => $paginatorData,
            'products' => $products,
            'filterAlias' => $filterAlias,
        );
        if ($breadcrumbsCategories) {
            $returnArray['breadcrumbsCategories'] = $breadcrumbsCategories;
        }
        if ($categoryChildren && $categoryChildren->count() > 0) {
            $returnArray['categoryChildren'] = $categoryChildren;
        }
        $returnArray['menuItems'] = $this->menuItems;
        return $this->render('AppBundle:Default:filter.html.twig', $returnArray);
    }

    /**
     * @Route("/product/buy/{alias}", name="product_buy_route")
     */
    public function productBuyAction($alias, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em
            ->getRepository('AppBundle:Product')
            ->findOneBy(array('alias' => $alias));
        if (!$product) {
            throw $this->createNotFoundException('The product does not exist');
        }
        $newStat = new Stat();
        $newStat->setProductId($product->getId());
        $this->checkAndInsertFilterAliasByProduct($product);
        if ($request->getClientIp()) {
            $newStat->setClientIp($request->getClientIp());
        }
        $em->persist($newStat);
        $em->flush();
        return $this->redirect($product->getURl());
    }

    /**
     * @Route("/search", name="search")
     */
    public function searchAction(Request $request) {
        $searchd = $this->get('iakumai.sphinxsearch.search');
        $searchString = $request->request->get('searchString', '');
//        $searchString = str_replace(' ', '*', $searchString);
        $data = $searchd->search($searchString, array('FilterAlias'));
        $options = array();
        $searchArray = array();
        $em = $this->getDoctrine()->getManager();
        if (isset($data['matches'])) {
            $i = 0;
            foreach ($data['matches'] as $match) {
                if ($i >=20) {
                    break;
                }
                $filterAlias = $em
                    ->getRepository('AppBundle:FilterAlias')
                    ->findOneBy(array('alias' => $match['attrs']['alias']));
                $searchArray[$filterAlias->getAliasText()] = $match['attrs']['alias'];
                $options[] = $filterAlias->getAliasText();
                $i++;
            }
        }
        $response = new JsonResponse();
        $response->setData(array(
            'options' => $options,
            'searchArray' => $searchArray,
        ));
        return $response;
    }

    /**
     * @param Category $parentCategory
     * @return array
     */
    public function getChildCategoryIds($parentCategory)
    {
        $childExCategoryIds = array();
        $childCategories = $parentCategory->getChildren();
        $childExCategories = $parentCategory->getExternalCategories();
        foreach ($childExCategories as $childExCategory) {
            $childExCategoryIds[] = $childExCategory->getId();
        }
        /** @var Category $childCategory */
        foreach ($childCategories as $childCategory) {
            $childExCategories = $childCategory->getExternalCategories();
            foreach ($childExCategories as $childExCategory) {
                $childExCategoryIds[] = $childExCategory->getId();
            }
        }
        return $childExCategoryIds;
    }

    private function getMenuItems()
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('Category')
            ->from('AppBundle:Category', 'Category')
            ->where('Category.isActive = 1')
            ->andWhere('Category.parent is NULL')
            ->setMaxResults(20);
        $query = $qb->getQuery();
        $resultCategories = $query->getResult();
        /** @var Category $resultCategory */
        foreach ($resultCategories as $resultCategory) {
            $count = 0;
            $childExCategoryIds = $this->getChildCategoryIds($resultCategory);
            $qb = $em->createQueryBuilder();
            $qb->select('exCategory.id, exCategory.name, count(Product.id) as cnt')
                ->from('AppBundle:ExternalCategory', 'exCategory')
                ->leftJoin('exCategory.products', 'Product')
                ->where('exCategory IN (:childCategories)')
                ->andWhere('exCategory.isActive = 1')
                ->having('cnt > 0')
                ->orderBy('cnt', 'DESC')
                ->setParameter('childCategories', $childExCategoryIds);
            $query = $qb->getQuery();
            $resultChildCategories = $query->getResult();
            foreach ($resultChildCategories as $resultChildCategory) {
                $count += $resultChildCategory['cnt'];
            }
            $this->menuItems['categories'][$count] = $resultCategory;
        }
        if (isset($this->menuItems['categories'])) {
            krsort($this->menuItems['categories']);
        }

        $this->menuItems['sites'] = $em
            ->getRepository('AppBundle:Site')
            ->findAll();
        $qb = $em->createQueryBuilder();
        $qb->select('Vendor.alias, Vendor.name, count(p.id) as cnt')
            ->from('AppBundle:Vendor', 'Vendor')
            ->leftJoin('Vendor.products', 'p')
            ->where('Vendor.isActive = 1')
            ->having('cnt > 500')
            ->groupBy('Vendor.id')
            ->orderBy('cnt', 'DESC')
            ->setMaxResults(15);
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
        $this->metaTags['metaTitle'] = 'Купить современную одежду с доставкой. Свитшоты, пуловеры, платья и шорты.';
        $this->metaTags['metaDescription'] = 'У нас Вы найдете качественную современную одежду для всей семьи!';
        $this->metaTags['metaKeywords'] = 'cвитшоты, пуловеры, платья, шорты, молодежная и детская одежда';
        $this->metaTags['metaRobots'] = 'all';
    }

    private function getPaginatorData($itemsCount, $currentPage, $limit, $midRange, $path = '/page/')
    {
        $paginator = new \AppBundle\Helpers\Paginator($itemsCount, $currentPage, $limit, $midRange);
        return array(
            'paginator' => $paginator,
            'path' => $path,
        );
    }

    private function getBreadcrumbs($item, $type)
    {
        switch ($type) {
            case 'product':
                $itemParentCategory = $item->getCategory();
                if ($itemParentCategory) {
                    $internalCategory = $itemParentCategory->getInternalParentCategory();
                    if ($internalCategory) {
                        $this->breadcrumbsCategories[] = $internalCategory;
                    }
                    $this->getBreadcrumbs($itemParentCategory, 'category');
                }
                break;
            case 'category':
                if ($item) {
                    if ($item instanceof ExternalCategory) {
                        $item = $item->getInternalParentCategory();
                    }
                    if (!in_array($item, $this->breadcrumbsCategories)) {
                        $this->breadcrumbsCategories[] = $item;
                    }
                    $itemParent = $item->getParent();
                    if ($itemParent) {
                        $this->breadcrumbsCategories[] = $itemParent;
                        $this->getBreadcrumbs($itemParent, 'category');
                    }
                }
        }
    }

    /**
     * @param $parseAliasLevelTwo
     * @param $qb
     * @return mixed
     */
    public function getQbByAlias($parseAliasLevelTwo, $qb)
    {
        $em = $this->getDoctrine()->getManager();
        $valueIds = array();
        foreach ($parseAliasLevelTwo as $parseAliasItem) {
            switch ($parseAliasItem['name']) {
                case 'category':
                    $category = $em
                        ->getRepository('AppBundle:Category')
                        ->findOneBy(array(
                            'alias' => $parseAliasItem['alias']
                        ));
                    if ($category) {
                        $qb->andWhere('Product.category IN (:childCategories)')
                            ->setParameter('childCategories', $this->getChildCategoryIds($category));
                    } else {
                        throw $this->createNotFoundException('Alias does not parsed (category)');
                    }
                    break;
                case 'vendor':
                    $vendor = $em
                        ->getRepository('AppBundle:Vendor')
                        ->findOneBy(array(
                            'alias' => $parseAliasItem['alias']
                        ));
                    if ($vendor) {
                        $qb->andWhere('Product.vendor = :vendorId')
                            ->setParameter('vendorId', $vendor->getId());
                    } else {
                        throw $this->createNotFoundException('Alias does not parsed (vendor)');
                    }
                    break;
                case 'param':
                    $value = $em
                        ->getRepository('AppBundle:ProductPropertyValue')
                        ->findOneBy(array(
                            'alias' => $parseAliasItem['alias']
                        ));
                    if ($value) {
                        $valueIds[] = $value->getId();
                    } else {
                        throw $this->createNotFoundException('The value does not exist (param)');
                    }
                    break;
                case 'attr':
                    break;
            }
        }
        if ($valueIds) {
            foreach ($valueIds as $valueId) {
                $qb->innerJoin('Product.productPropertyValues','ppv_' . $valueId)
                    ->andWhere('ppv_' . $valueId . ' = ' . $valueId);
            }
        }
        return $qb;
    }

    private function checkAndInsertFilterAliasByProduct($product)
    {
        $em = $this->getDoctrine()->getManager();
        $arrayToWrite = array();
        $filterAliasArray = array();

        /** @var ExternalCategory $productCategory */
        $productCategory = $product->getCategory();
        if ($productCategory) {
            $mainCategory = $productCategory->getInternalParentCategory();
            if ($mainCategory && $mainCategory->getAlias()) {
                $mainCategoryAlias = $mainCategory->getAlias();
                $mainCategoryName = $mainCategory->getName();
                $filterAliasArray['category'] = $arrayToWrite[] = array(
                    'alias' => 'category+' . $mainCategoryAlias,
                    'name' => $mainCategoryName,
                );
            }
        }

        /** @var Vendor $productVendor */
        $productVendor = $product->getVendor();
        if ($productVendor && $productVendor->getAlias()) {
            $vendorName = $productVendor->getName();
            $vendorAlias = $productVendor->getAlias();
            $filterAliasArray['vendor'] = $arrayToWrite[] = array(
                'alias' => 'vendor+' . $vendorAlias,
                'name' => $vendorName,
            );
        }

        if (isset($filterAliasArray['category']) && isset($filterAliasArray['vendor'])) {
            $arrayToWrite[] = array(
                'alias' => $filterAliasArray['category']['alias'] . '__' . $filterAliasArray['vendor']['alias'],
                'name' => $filterAliasArray['category']['name'] . ' ' . $filterAliasArray['vendor']['name']
            );
        }

        $productPropertyValues = $product->getProductPropertyValues();
        foreach ($productPropertyValues as $productPropertyValue) {
            $productPropertyValueAlias = $productPropertyValue->getAlias();
            if ($productPropertyValueAlias) {
                foreach ($filterAliasArray as $filterAlias) {
                    $arrayToWrite[] = array(
                        'alias' => $filterAlias['alias'] . '__' . 'param+' . $productPropertyValueAlias,
                        'name' => $filterAlias['name'] . ' ' . $productPropertyValue->getPropValue()
                    );
                }
            }
        }

        if ($filterAliasArray) {
            foreach ($arrayToWrite as $itemToWrite) {
                $filterAlias = $em
                    ->getRepository('AppBundle:FilterAlias')
                    ->findOneBy(array('alias' => $itemToWrite['alias']));
                if (!$filterAlias) {
                    $newFilterAlias = new FilterAlias();
                    $newFilterAlias->setAlias($itemToWrite['alias']);
                    $newFilterAlias->setAliasText(mb_strtolower($itemToWrite['name'], 'UTF-8'));
                    $em->persist($newFilterAlias);
                    $em->flush();
                }
            }
        }
    }
}
