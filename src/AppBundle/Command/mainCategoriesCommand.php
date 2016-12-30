<?php
namespace AppBundle\Command;

use AppBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class mainCategoriesCommand extends ContainerAwareCommand
{
    protected $output;

    protected $delimer = '----------';

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
        $qb = $this->em->createQueryBuilder();

        $qb->select('Product')
            ->from('AppBundle:Product', 'Product')
            ->where('Product.isDelete = 0')
            ->groupBy('Product.category');
        $query = $qb->getQuery();
        $products = $query->getResult();
        $productCategories = array();
        foreach ($products as $product) {
            $productCategoryId = $product->getCategory()->getId();
            $productCategories[$productCategoryId] = $productCategoryId;
        }
        $this->outputWriteLn('Categories - ' . count($productCategories));

        $qb = $this->em->createQueryBuilder();
        $qb->select('ExternalCategory')
            ->from('AppBundle:ExternalCategory', 'ExternalCategory')
            ->where('ExternalCategory.id IN (:productCategories)')
            ->setParameter('productCategories', $productCategories);
        $query = $qb->getQuery();
        $categories = $query->getResult();
        $this->outputWriteLn('CategoriesForWrite - ' . count($categories));
        foreach ($categories as $category) {
            $categoryParentId = $category->getInternalParentCategory();
            if (!$categoryParentId) {
                $categoryName = $category->getName();
                $mainCategory = $this->em
                    ->getRepository('AppBundle:Category')
                    ->findOneBy(array(
                        'name' => $categoryName
                    ));
                if (!$mainCategory) {
                    $mainCategory = new Category();
                    $mainCategory->setName($categoryName);
                    $mainCategory->setIsActive(true);
                    $this->em->persist($mainCategory);
                    $this->em->flush();
                }
                $category->setInternalParentCategory($mainCategory);
                $this->em->flush();
            }
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