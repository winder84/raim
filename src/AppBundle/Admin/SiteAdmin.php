<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class SiteAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
//        $datagridMapper
//            ->add('id')
//            ->add('title')
//            ->add('description')
//            ->add('xmlParseUrl')
//            ->add('deliveryUrl')
//            ->add('paymentUrl')
//            ->add('alias')
//        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, array('label' => 'Id'))
            ->add('title', null, array('label' => 'Наименование'))
            ->add('alias', null, array('label' => 'Alias'))
            ->add('url', null, array('label' => 'Url'))
            ->add('version', null, array('label' => 'Версия', 'required' => false))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                ),
                'label' => 'Действия'
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', null, array('label' => 'Наименование'))
            ->add('description', 'ckeditor', array('label' => 'Описание', 'required' => false))
            ->add('xmlParseUrl', null, array('label' => 'Xml url'))
            ->add('deliveryUrl', null, array('label' => 'Url доставки', 'required' => false))
            ->add('paymentUrl', null, array('label' => 'Url страницы оплаты', 'required' => false))
            ->add('logoUrl', null, array('label' => 'Url логотипа', 'required' => false))
            ->add('alias', null, array('label' => 'Alias', 'required' => false))
            ->add('url', null, array('label' => 'Url'))
            ->add('seoDescription', null, array('label' => 'SEO Описание', 'required' => false))
            ->add('seoKeywords', null, array('label' => 'SEO Ключевые слова', 'required' => false))
            ->add('updatePeriod', null, array('label' => 'Период обновления (ч)', 'required' => false))
            ->add('version', null, array('label' => 'Версия'))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('title')
            ->add('description')
            ->add('xmlParseUrl')
            ->add('deliveryUrl')
            ->add('paymentUrl')
            ->add('alias')
            ->add('url')
        ;
    }
}
