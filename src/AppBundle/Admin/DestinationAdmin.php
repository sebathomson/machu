<?php
/**
 * Created by PhpStorm.
 * User: alhorro
 * Date: 23.12.2015
 * Time: 20:10
 */

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;


class DestinationAdmin extends Admin {

    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper->add('name');
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper->addIdentifier('name');
    }


}