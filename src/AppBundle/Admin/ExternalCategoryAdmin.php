<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ExternalCategoryAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('externalId', null, array('label' => 'Внешний id'))
            ->add('parentId', 'doctrine_orm_callback', array(
                    'label' => 'Родительский id',
                    'callback' => array($this, 'getParentIdFilter'),
                )
            )
            ->add('internalParentCategory', null, array('label' => 'Внутренняя категория'))
            ->add('name', null, array('label' => 'Наименование'))
            ->add('site', null, array('label' => 'Магазин'))
            ->add('version', null, array('label' => 'Версия'))
            ->add('isActive', null, array(
                    'label'    => 'Вкл.',
                    'required' => false,
                )
            )
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('externalId', null, array('label' => 'Внешний id'))
            ->add('parentId', null, array('label' => 'Родительский id'))
            ->add('internalParentCategory', null, array('label' => 'Внутренняя категория'))
            ->add('name', null, array('label' => 'Наименование'))
            ->add('site', null, array('label' => 'Магазин'))
            ->add('ourChoice', null, array(
                    'label'    => 'Наш выбор',
                    'required' => false,
                )
            )
            ->add('version', null, array('label' => 'Версия'))
            ->add('isActive', null, array(
                    'label'    => 'Вкл.',
                    'required' => false,
                )
            )
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
            ->add('isActive', null, array(
                    'label'    => 'Вкл.',
                    'required' => false,
                )
            )
            ->add('ourChoice', null, array(
                    'label'    => 'Наш выбор',
                    'required' => false,
                )
            )
            ->add('name', null, array('label' => 'Наименование'))
            ->add('externalId', null, array('label' => 'Внешний id'))
            ->add('parentId', null, array('label' => 'Родительский id'))
            ->add('version', null, array('label' => 'Версия'))
            ->add('internalParentCategory', null, array('label' => 'Внутренняя категория'))
            ->add('site', null, array('label' => 'Магазин'))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('externalId')
            ->add('parentId')
            ->add('name')
            ->add('version')
        ;
    }


    /**
     * получить equal parentId
     * @param $queryBuilder
     * @param $alias
     * @param $field
     * @param $value
     * @return bool
     */
    public function getParentIdFilter($queryBuilder, $alias, $field, $value)
    {
        if (!isset($value['value'])) {
            return;
        }

        $queryBuilder->andWhere("$alias.parentId = :value");
        $queryBuilder->setParameter('value', $value['value']);

        return true;
    }
}
