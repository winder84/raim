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
        $siteVersion = round($site->getVersion(), 2);
        $qb = $this->em->createQueryBuilder();
        $nowDateTime = new \DateTime();
        $dateTimeToDelete = $nowDateTime->sub(new \DateInterval('P30D'))->format("Y-m-d H:i:s");
        $qb->delete('AppBundle:Product', 'Product')
            ->where('Product.site = :site')
            ->andWhere('round(Product.version, 2) != :newVersion')
            ->andWhere('Product.updated < :dateTimeToDelete')
            ->andWhere('Product.isDelete = 1')
            ->setParameter('site', $site)
            ->setParameter('newVersion', $siteVersion)
            ->setParameter('dateTimeToDelete', $dateTimeToDelete);
        $qb->getQuery()->execute();
        $this->outputWriteLn('Offers deleted');
        $this->em->flush();
        $this->em->clear();

        $qb = $this->em->createQueryBuilder();
        $qb->update('AppBundle:Product', 'Product')
            ->set('Product.isDelete', '1')
            ->set('Product.updated', ':nowDateTime')
            ->where('Product.site = :site')
            ->andWhere('round(Product.version, 2) != :newVersion')
            ->setParameter('site', $site)
            ->setParameter('nowDateTime', $nowDateTime->format("Y-m-d H:i:s"))
            ->setParameter('newVersion', $siteVersion);
        $qb->getQuery()->execute();
        $this->outputWriteLn('Offers updated');
        $this->em->flush();
        $this->em->clear();

        $qb = $this->em->createQueryBuilder();
        $qb->delete('AppBundle:ExternalCategory', 'ExCategory')
            ->where('ExCategory.site = :site')
            ->andWhere('round(ExCategory.version, 2) != :newVersion')
            ->setParameter('site', $site)
            ->setParameter('newVersion', $siteVersion);
        $qb->getQuery()->execute();
        $this->outputWriteLn('ExCategories deleted');
        $this->em->flush();
        $this->em->clear();

        $qb = $this->em->createQueryBuilder();
        $qb->delete('AppBundle:Vendor', 'Vendor')
            ->where('Vendor.site = :site')
            ->andWhere('round(Vendor.version, 2) != :newVersion')
            ->setParameter('site', $site)
            ->setParameter('newVersion', $siteVersion);
        $qb->getQuery()->execute();
        $this->outputWriteLn('Vendors deleted');
        $this->em->flush();
        $this->em->clear();
    }
}