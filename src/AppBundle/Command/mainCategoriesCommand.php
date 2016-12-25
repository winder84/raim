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
        $categories = $this->em
                ->getRepository('AppBundle:ExternalCategory')
                ->findBy(array(
                    'parentId' => array(null, '', 0)
                ));
        foreach ($categories as $category) {
            $categoryParentId = $category->getParentId();
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