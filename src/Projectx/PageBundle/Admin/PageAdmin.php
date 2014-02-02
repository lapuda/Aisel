<?php

namespace Projectx\PageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;


class PageAdmin extends Admin
{

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', array('description' => 'This section contains general settings for the web page'))
                ->add('title', 'text', array('label' => 'Post Title'))
                ->add('content', 'textarea', array('label' => 'Content'))
                ->add('page_status', 'choice', array('choices'   => array(
                        '0'   => 'Draft',
                        '1' => 'Published'),
                        'label' => 'Status'
                    ))
                ->add('comment_status', 'choice', array('choices'   => array(
                    '0'   => 'Disabled',
                    '1' => 'Enabled'),
                    'label' => 'Comments'
                ))
                ->add('categories', 'y_tree', array('expanded' => true,'multiple' => true,
                    'class' => 'Projectx\CategoryBundle\Entity\Category',
                ))
//                ->add('categories', 'sonata_type_model',array('property' => 'spacedtitle','expanded' => true, 'compound' => true, 'multiple' => true))
//                ->add('categories', 'sonata_type_collection', array(), array(
//                    'edit' => 'inline',
//                    'inline' => 'table',
//                    'sortable'  => 'id'
//                ))
            ->with('Meta', array('description' => 'Meta description for search engines'))
                ->add('meta_url', 'text', array('label' => 'Url slug'))
                ->add('meta_Title', 'text', array('label' => 'Title'))
                ->add('meta_description', 'textarea', array('label' => 'Description'))
                ->add('meta_keywords', 'textarea', array('label' => 'Keywords'))
            ->end();

    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('content')
        ;
    }

    public function prePersist($page)
    {
        $page->setDateCreated(new \DateTime(date('Y-m-d H:i:s')));
        $page->setDateModified(new \DateTime(date('Y-m-d H:i:s')));
    }

    public function preUpdate($page)
    {
        $page->setDateModified(new \DateTime(date('Y-m-d H:i:s')));
    }


    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title')
            ->add('content', null, array('template' => 'ProjectxPageBundle:Admin:content.html.twig', 'label'=>'Content','true'=>false))
            ->add('pageStatus', 'boolean', array('label' => 'Status','editable' => true))
            ->add('commentStatus', 'boolean', array('label' => 'Comments','editable' => true))
            ->add('dateModified', 'datetime', array('label' => 'Date'))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'show' => array(),
                        'edit' => array(),
                        'delete' => array(),
                    ))
            );
        ;
    }

}