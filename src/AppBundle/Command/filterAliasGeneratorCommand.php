<?php
namespace AppBundle\Command;

use AppBundle\Entity\FilterAlias;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class filterAliasGeneratorCommand extends ContainerAwareCommand
{
    protected $output;

    protected $delimer = '----------';

    protected $em;

    protected $aliasesArray = array();

    protected function configure()
    {
        $this
            ->setName('kins:faligen')
            ->setDescription('Generate filter aliases')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $this->output = $output;
        $this->outputWriteLn('Start generate filter aliases');
        $filterAliasObjects = $this->em
                ->getRepository('AppBundle:FilterAlias')
                ->findAll();
        foreach ($filterAliasObjects as $filterAliasObject) {
            $this->aliasesArray[$filterAliasObject->getAlias()] = $filterAliasObject->getAliasText();
        }
        $categories = $this->em
                ->getRepository('AppBundle:Category')
                ->findAll();
        foreach ($categories as $category) {
            if (!array_key_exists('category+' . $category->getAlias(), $this->aliasesArray)) {
                $this->aliasesArray['category+' . $category->getAlias()] = $category->getName();
            }
        }
        $vendors = $this->em
            ->getRepository('AppBundle:Vendor')
            ->findAll();
        foreach ($categories as $category) {
            foreach ($vendors as $vendor) {
                if (!array_key_exists('category+' . $category->getAlias() . '__vendor+' . $vendor->getAlias(), $this->aliasesArray)) {
                    $this->aliasesArray['category+' . $category->getAlias() . '__vendor+' . $vendor->getAlias()] = $category->getName() . ' ' . $vendor->getName();
                }
            }
        }
//        $qb = $this->em->createQueryBuilder();
//        $qb->select('ProductPropertyValue')
//            ->from('AppBundle:ProductPropertyValue', 'ProductPropertyValue')
//            ->where('ProductPropertyValue.alias IS NOT NULL');
//        $query = $qb->getQuery();
//        $propertyValues = $query->getResult();
//        foreach ($propertyValues as $propertyValue) {
//            $this->checkAliases($propertyValue);
//        }
        $this->outputWriteLn('Count - ' . count($this->aliasesArray));

        $i = 0;
        $j = 0;
        foreach ($this->aliasesArray as $aliasKey => $aliasValue) {
            $j++;
            $qb = $this->em->createQueryBuilder();
            $qb->select('FilterAlias')
                ->from('AppBundle:FilterAlias', 'FilterAlias')
                ->where('FilterAlias.alias = :alias')
                ->setParameter(':alias', $aliasKey);
            $query = $qb->getQuery();
            $propertyValues = $query->getResult();
            if (count($propertyValues) == 0) {
                if ($this->getProducts($aliasKey)) {
//                    $i++;
//                    $newFilterAlias = new FilterAlias();
//                    $newFilterAlias->setAlias($aliasKey);
//                    $newFilterAlias->setAliasText(mb_strtolower($aliasValue, 'UTF-8'));
//                    $this->em->persist($newFilterAlias);
//                    $this->em->flush();
//                    if ($i % 1000 == 0) {
//                        $this->outputWriteLn('Write aliases - ' . $i);
//                    }
                }
            }
            $propertyValues = null;
            if ($j % 10000 == 0) {
                $this->outputWriteLn('Read aliases - ' . $j);
            }
            $this->em->clear();
        }

        $this->outputWriteLn('End generate filter aliases');
    }

    private function outputWriteLn($text)
    {
        $newTimeDate = new \DateTime();
        $newTimeDate = $newTimeDate->format(\DateTime::ATOM);
        $this->output->writeln($this->delimer. $newTimeDate . ' ' . $text . ' Memory usage: ' . round(memory_get_usage() / (1024 * 1024)) . ' MB' . $this->delimer);
    }

//    private function checkAliases($propertyValue)
//    {
//        $alias = $propertyValue->getAlias();
//        $propValue = $propertyValue->getPropValue();
//        foreach ($this->aliasesArray as $aliasKey => $aliasValue) {
//            $newAlias = $aliasKey . '__param+' . $alias;
//            if (strlen($newAlias) < 100 && strlen($aliasKey) < 100 ) {
//                $parseAliasLevelOne = explode('__', $aliasKey);
//                $parseAliasLevelTwo = array();
//                if (!$parseAliasLevelOne) {
//                    return null;
//                }
//                foreach ($parseAliasLevelOne as $parseAliasItem) {
//                    list($key, $value) = explode('+', $parseAliasItem);
//                    $parseAliasLevelTwo[] = array(
//                        'name' => $key,
//                        'alias' => $value,
//                    );
//                }
//                if (count($parseAliasLevelTwo) < 2) {
//                    if (!isset($this->aliasesArray[$newAlias]) && (strpos($aliasKey, $alias) === false)) {
//                        $this->aliasesArray[$newAlias] = $aliasValue . ' ' . $propValue;
//                        if (count($this->aliasesArray) % 20000 == 0) {
//                            $this->outputWriteLn('Aliases - ' . count($this->aliasesArray));
//                        }
//                    }
//                }
//            }
//            $this->em->clear();
//        }
//    }

    public function getQbByAlias($alias)
    {
        $parseAliasLevelOne = explode('__', $alias);
        $parseAliasLevelTwo = array();
        if (!$parseAliasLevelOne) {
            return null;
        }
        foreach ($parseAliasLevelOne as $parseAliasItem) {
            list($key, $value) = explode('+', $parseAliasItem);
            $parseAliasLevelTwo[] = array(
                'name' => $key,
                'alias' => $value,
            );
        }
        if (!$parseAliasLevelTwo) {
            return null;
        }
        $qb = $this->em->createQueryBuilder();
        $qb->select('Product')
            ->from('AppBundle:Product', 'Product')
            ->where('Product.isDelete = 0');
        $valueIds = array();
        foreach ($parseAliasLevelTwo as $parseAliasItem) {
            if (isset($parseAliasItem['name'])) {
                switch ($parseAliasItem['name']) {
                    case 'category':
                        $category = null;
                        if (isset($parseAliasItem['alias'])) {
                            $category = $this->em
                                ->getRepository('AppBundle:Category')
                                ->findOneBy(array(
                                    'alias' => $parseAliasItem['alias']
                                ));
                        }
                        if ($category) {
                            $childCategoriesIds = $this->getChildCategoriesIds($category->getId());
                            $qb->andWhere('Product.category IN (:childCategoriesIds)')
                                ->setParameter('childCategoriesIds', $childCategoriesIds);
                        } else {
                            return null;
                        }
                        break;
                    case 'vendor':
                        $vendor = null;
                        if (isset($parseAliasItem['alias'])) {
                            $vendor = $this->em
                                ->getRepository('AppBundle:Vendor')
                                ->findOneBy(array(
                                    'alias' => $parseAliasItem['alias']
                                ));
                        }
                        if ($vendor) {
                            $qb->andWhere('Product.vendor = :vendorId')
                                ->setParameter('vendorId', $vendor->getId());
                        } else {
                            return null;
                        }
                        break;
//                    case 'param':
//                        if (isset($parseAliasItem['alias'])) {
//                            $value = $em
//                                ->getRepository('AppBundle:ProductPropertyValue')
//                                ->findOneBy(array(
//                                    'alias' => $parseAliasItem['alias']
//                                ));
//                        }
//                        if ($value) {
//                            $valueIds[] = $value->getId();
//                        } else {
//                            return null;
//                        }
//                        break;
//                    case 'attr':
//                        break;
                }
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

    private function getChildCategoriesIds($parentCategoryId)
    {
        $resultCategoriesIds = array();
        $parentCategoryIds = array();
        $qb = $this->em->createQueryBuilder();
        $qb->select('ExCategory.externalId')
            ->from('AppBundle:ExternalCategory', 'ExCategory')
            ->where('ExCategory.internalParentCategory = :parentCategoryId')
            ->andWhere('ExCategory.isActive = 1')
            ->setParameter('parentCategoryId', $parentCategoryId);
        $query = $qb->getQuery();
        $exCategoriesIds = $query->getResult();
        foreach ($exCategoriesIds as $exCategoriesId) {
            $parentCategoryIds[] = $exCategoriesId['externalId'];
        }
        $qb = $this->em->createQueryBuilder();
        $qb->select('ExCat.id')
            ->from('AppBundle:ExternalCategory', 'ExCat')
            ->where('ExCat.parentId IN (:parentCategoryIds)')
            ->orWhere('ExCat.externalId IN (:parentCategoryIds)')
            ->andWhere('ExCat.isActive = 1')
            ->setParameter('parentCategoryIds', $parentCategoryIds);
        $query = $qb->getQuery();
        $exCatIds = $query->getResult();
        foreach ($exCatIds as $exCategoriesId) {
            $resultCategoriesIds[] = $exCategoriesId['id'];
        }
        return $resultCategoriesIds;
    }

    private function getProducts($alias)
    {
        $qb = $this->getQbByAlias($alias);
        $results = array();
        if ($qb) {
            $query = $qb->getQuery()
                ->setMaxResults(30);
            $results = $query->getResult();
        }
        if (count($results) > 29) {
            return true;
        }
        return false;
    }
}