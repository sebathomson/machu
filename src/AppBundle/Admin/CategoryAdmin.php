<?php
/**
 * Created by PhpStorm.
 * User: alhorro
 * Date: 08.12.2015
 * Time: 19:02
 */

namespace AppBundle\Admin;

use AppBundle\Entity\TourDate;
use Carbon\Carbon;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class CategoryAdmin extends Admin {

    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper
        ->add('name')
        ->add('destination')
        ->add('date', 'entity', array(
            'required' => false,
            'class'    => 'AppBundle\Entity\TourDate',
            'choices'  => $this->getSubject()->getId() ? $this->getDates() : array()
            //'attr' => array('data-sonata-select2'=>'false'),
            )
        )
        ->add('custom_availability', 'number', 
            array(
                'label'    => 'Space Available',
                'required' => false,
                'help'     =>"Only if destination is 'CUSTOM'",
                )
            )
        ->add('minAvailability');


        if ($this->getSubject()->getId() && $this->getSubject()->getDestination()->getId() == 10) {
            // Es un EDITAR!
            $formMapper->add('custom_date', 'date', array(
                'required'  => false,
                'read_only' => true,
                'widget'    => 'single_text',
                )
            );
        } else {
            $formMapper->add('custom_date', 'date', array(
                'required' => false,
                'widget'   => 'single_text'
                )
            );
        }



        if($this->getSubject()->getId() && !$this->getSubject()->getDate()){
            $date = new Carbon();
            $date->day += 5;
            $de = $this->getConfigurationPool()->getContainer()->get('Doctrine')->getRepository('AppBundle:TourDate')->findOneBy(array(
                'destination' => $this->getSubject()->getDestination(),
                'date' => $date,
                ));
            if($de) $this->getSubject()->setDate($de);
        }

    }

    public function getDates(){
        $date = new Carbon();
        $qb = $this->getConfigurationPool()->getContainer()->get('Doctrine')->getRepository('AppBundle:TourDate')->createQueryBuilder('d');
        $qb->andWhere('d.destination = :c')->setParameter('c', $this->getSubject()->getDestination());
        $qb->andWhere('d.date >= :d')->setParameter('d', $date);
        $qb->orderBy('d.date', 'ASC');
        return $qb->getQuery()->getResult();
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper->addIdentifier('id', 'string', array(
            'template' => 'admin/catLink.html.twig'
            ))->addIdentifier('name')->addIdentifier('date');
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        if ($this->getSubject()->getDestination()->getId() == 10) {
            $errorElement
            ->with('custom_availability')
            ->assertNotNull()
            ->assertNotBlank()
            ->assertLength(array('min' => 1))
            ->end();

            $errorElement
            ->with('custom_date')
            ->assertNotNull()
            ->assertNotBlank()
            ->end();
        }
    }
}