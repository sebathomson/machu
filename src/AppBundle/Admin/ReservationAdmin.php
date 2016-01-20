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
        ->add('tourDate.category.destination', null, array(
            'read_only' => true,
            'disabled'  => true,
            ))
        ->add('tourDate.category', null, array(
            'read_only' => true,
            'disabled'  => true,
            ))
        ->add('status', 'choice', array('choices'=>array(
            'pending'=>'pending',
            'rejected'=>'rejected',
            'approved'=>'approved',
            )));

        if( $this->getSubject()->getId() && $this->getSubject()->getTourDate()->getCategory()->getDestination()->getId() == 10 ){
            $formMapper->add('tourDate.date', 'date', array(
                'read_only' => true,
                'disabled'  => true,
                ));
        } else {
            $formMapper->add('tourDate.category.date.date', 'date', array(
                'read_only' => true,
                'disabled'  => true,
                ));
        }



    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper->addIdentifier('name')
        ->addIdentifier('email')
        ->addIdentifier('country')
        ->addIdentifier('tourDate.date', null, array(
            'label' => 'Category Date INC'
            )
        )
        ->addIdentifier('tourDate.category.destination.name')
        ->addIdentifier('tourDate.category.name')
        ->addIdentifier('status');
    }

}