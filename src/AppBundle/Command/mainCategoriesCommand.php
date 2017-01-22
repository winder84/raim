<?php
namespace AppBundle\Command;

use AppBundle\Entity\Category;
use AppBundle\Entity\ExternalCategory;
use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class mainCategoriesCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface
     */
    protected $output;

    protected $delimer = '----------';

    /**
     * @var EntityManager
     */
    protected $em;

    protected function configure()
    {
        $this
            ->setName('kins:mcat')
            ->setDescription('Generate main categories')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $this->output = $output;
        $this->outputWriteLn('Start generate main categories');

        // todo: а можно и так (с) alitvinenko
//        $repo = $this->em->getRepository(Product::class);
//        $products = $repo
//            ->createQueryBuilder('p')
//            ->where('p.isDelete = 0')
//            ->groupBy('p.category')
//            ->getQuery()
//            ->getResult()
//        ;

        $qb = $this->em->createQueryBuilder();


        // со временем этот запрос у тебя будет работать несколко секунд когда товаров в базе будет дофига
        $qb->select('Product')
            ->from('AppBundle:Product', 'Product')
            ->where('Product.isDelete = 0')
            ->groupBy('Product.category')
        ;
        $query = $qb->getQuery();
        $products = $query->getResult();
        $productCategories = array();

        /** @var Product[] $products */
        foreach ($products as $product) {
            $productCategoryId = $product->getCategory()->getId();
            $productCategories[$productCategoryId] = $productCategoryId;
        }
        $this->outputWriteLn('Categories - ' . count($productCategories));

        $qb = $this->em->createQueryBuilder();

        // тут может быть очень много категорий и запрос тупо отвалится или будет есть очень много памяти. можно разбивать на несколько запросов
        // ну, хуй знает. поживем, увидим.
        $categories = $qb->select('ExternalCategory')
            ->from('AppBundle:ExternalCategory', 'ExternalCategory')
            ->where('ExternalCategory.id IN (:productCategories)')
            ->setParameter('productCategories', $productCategories)
            ->getQuery()
            ->getResult()
        ;
//        $query = $qb->getQuery();
        /** @var ExternalCategory[] $categories */
//        $categories = $query->getResult();

        $this->outputWriteLn('CategoriesForWrite - ' . count($categories));
        foreach ($categories as $category) {
            $categoryParent = $category->getInternalParentCategory();

            if ($categoryParent) {
                continue;
            }

            $categoryName = $category->getName();
            $mainCategory = $this->em
                ->getRepository('AppBundle:Category')
                ->findOneBy([
                    'name' => $categoryName
                ])
            ;

            if (!$mainCategory) {
                $mainCategory = new Category();
                $mainCategory->setName($categoryName);
                $mainCategory->setIsActive(true);
                $this->em->persist($mainCategory);
                $this->em->flush();
            }

            if (!$category->getInternalParentCategory()) {
                $category->setInternalParentCategory($mainCategory);
            }

            $this->em->flush();
        }

        $this->outputWriteLn('End generate main categories');
    }

    private function outputWriteLn($text)
    {
        $newTimeDate = new \DateTime();
        $newTimeDate = $newTimeDate->format(\DateTime::ATOM);
        $this->output->writeln($this->delimer. $newTimeDate . ' ' . $text . ' Memory usage: ' . round(memory_get_usage() / (1024 * 1024)) . ' MB' . $this->delimer);
    }

}