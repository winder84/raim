<?php
namespace AppBundle\Command;

use AppBundle\Entity\Vendor;
use AppBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class clearSiteCommand extends ContainerAwareCommand
{
    protected $output;

    protected $delimer = '----------';

    protected $em;

    protected function configure()
    {
        $this
            ->setName('kins:clear')
            ->setDescription('Parse markets')
            ->addArgument(
                'marketId',
                InputArgument::OPTIONAL,
                'Who do you want to parse?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $marketId = intval($input->getArgument('marketId'));
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        if ($marketId) {
            $sites = $this->em
                ->getRepository('AppBundle:Site')
                ->findBy(array('id' => $marketId));
        } else {
            $sites = $this->em
                ->getRepository('AppBundle:Site')
                ->findAll();
        }
        foreach ($sites as $site) {
            $this->outputWriteLn('Start clear offers ----- |' . $site->getTitle() . '| ----- ');
            $this->clearSite($site);
            $this->outputWriteLn('End clear offers ----- |' . $site->getTitle() . '| ----- ');
        }
    }

    private function outputWriteLn($text)
    {
        $newTimeDate = new \DateTime();
        $newTimeDate = $newTimeDate->format(\DateTime::ATOM);
        $this->output->writeln($this->delimer. $newTimeDate . ' ' . $text . ' Memory usage: ' . round(memory_get_usage() / (1024 * 1024)) . ' MB' . $this->delimer);
    }

    private function clearSite($site)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('Product')
            ->from('AppBundle:Product', 'Product')
            ->where('Product.site = :site')
            ->andWhere('Product.version != :newVersion')
            ->setParameter('site', $site)
            ->setParameter('newVersion', $site->getVersion());
        $query = $qb->getQuery();
        $productsToDelete = $query->getResult();
        $nowDateTime = new \DateTime();
        foreach ($productsToDelete as $productToDelete) {
            $productUpdated = $productToDelete->getUpdated();
            if ($productToDelete->getIsDelete()) {
                if ($nowDateTime->diff($productUpdated)->days >= 14) {
                    $deletedProductsArray[] = $productToDelete;
//                    $this->em->remove($productToDelete);
                }
            } else {
                $productToDelete->setIsDelete(true);
                $productToDelete->setUpdated(new \DateTime());
                $productsToDeleteArray[] = $productToDelete;
            }
        }
        $this->em->flush();
        $this->em->clear('AppBundle\Entity\Product');
        if (!empty($productsToDeleteArray)) {
            $this->outputWriteLn('Offers to delete - ' . count($productsToDeleteArray));
        }
        if (!empty($deletedProductsArray)) {
            $this->outputWriteLn('Deleted offers - ' . count($deletedProductsArray));
        }

        $qb = $this->em->createQueryBuilder();
        $qb->select('ExCategory')
            ->from('AppBundle:ExternalCategory', 'ExCategory')
            ->where('ExCategory.site = :site')
            ->andWhere('ExCategory.version != :newVersion')
            ->setParameter('site', $site)
            ->setParameter('newVersion', $site->getVersion());
        $query = $qb->getQuery();
        $exCategoriesToDelete = $query->getResult();
        foreach ($exCategoriesToDelete as $exCategoryToDelete) {
            $exCategoryToDeleteArray[] = $exCategoryToDelete;
            $this->em->remove($exCategoryToDelete);
        }
        $this->em->flush();
        $this->em->clear('AppBundle\Entity\ExternalCategory');
        if (!empty($exCategoryToDeleteArray)) {
            $this->outputWriteLn('Deleted categories - ' . count($exCategoryToDeleteArray));
        }

        $qb = $this->em->createQueryBuilder();
        $qb->select('Vendor')
            ->from('AppBundle:Vendor', 'Vendor')
            ->where('Vendor.site = :site')
            ->andWhere('Vendor.version != :newVersion')
            ->setParameter('site', $site)
            ->setParameter('newVersion', $site->getVersion());
        $query = $qb->getQuery();
        $vendorsToDelete = $query->getResult();
        foreach ($vendorsToDelete as $vendorToDelete) {
            $vendorsToDeleteArray[] = $vendorToDelete;
            $this->em->remove($vendorToDelete);
        }
        $this->em->flush();
        $this->em->clear('AppBundle\Entity\Vendor');
        if (!empty($vendorsToDeleteArray)) {
            $this->outputWriteLn('Deleted vendors - ' . count($vendorsToDeleteArray));
        }
    }
}