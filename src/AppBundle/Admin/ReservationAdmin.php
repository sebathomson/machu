<?php
/**
 * Created by PhpStorm.
 * User: alhorro
 * Date: 09.12.2015
 * Time: 10:38
 */

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ReservationAdmin extends Admin {

    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper->add('name')
        ->add('email')
        ->add('country', 'country')
        ->add('category.destination', null, array(
            'read_only' => true,
            'disabled'  => true,
            ))
        ->add('category', null, array(
            'read_only' => true,
            'disabled'  => true,
            ))
        ->add('status', 'choice', array('choices'=>array(
            'pending'=>'pending',
            'rejected'=>'rejected',
            'approved'=>'approved',
            )));

        if( $this->getSubject()->getId() && $this->getSubject()->getCategory()->getDestination()->getId() == 10 ){
            $formMapper->add('category.custom_date', 'date', array(
                'read_only' => true,
                'disabled'  => true,
                ));
        } else {
            $formMapper->add('category.date.date', 'date', array(
                'read_only' => true,
                'disabled'  => true,
                ));
        }



    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper->addIdentifier('name')
        ->addIdentifier('email')
        ->addIdentifier('country')
        ->addIdentifier('category.date.date', null, array(
            'label' => 'Category Date INC'
            )
        )
        ->add('category.custom_date', 'date')
        ->addIdentifier('category.destination.name')
        ->addIdentifier('category.name')
        ->addIdentifier('status');
    }

}