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
        $arrYears = array();

        foreach (range(Date('Y'), date('Y') + 4) as $year) {
            $arrYears[ $year ] = $year;
        }

        $formMapper->add('name');

        if ($this->getSubject()->getId() && $this->getSubject()->getDestination()->getId() == 10) {
            // Es un EDITAR!
            $formMapper->add('destination', 'hidden', 
                array(
                    'mapped'    => false,
                    'data'      => $this->getSubject()->getDestination()->getId(),
                    )
                );
            $formMapper->add('destination_read', 'text',
                array(
                    'mapped'    => false,
                    'read_only' => true,
                    'data'      => $this->getSubject()->getDestination()->getName(),
                    )
                );
            $formMapper->add('year', 'text', 
                array(
                    'read_only' => true,
                    'label'     => 'Year *',
                    'required'  => false,
                    )
                );
            $formMapper->add('baseAvailability', 'number', 
                array(
                    'read_only' => true,
                    'label'     => 'Base Availability',
                    'required'  => false,
                    'help'      =>"Only if destination is 'CUSTOM' destination",
                    )
                );
            $formMapper->add('dates', 'sonata_type_collection', 
                array(
                    'by_reference'       => true,
                    'label'              => false,
                    'type_options'       => array('delete' => false),
                    'cascade_validation' => true,
                    "required"           => false
                    ), 
                array(
                    'edit'   => 'inline',
                    'inline' => 'table'
                    )
                );
        } else {
            $formMapper->add('destination');
            $formMapper->add('year', 'choice', 
                array(
                    'label'    => 'Year *',
                    'required' => false,
                    'choices'  => $arrYears,
                    'data'     => Date('Y'),
                    )
                );
            $formMapper->add('baseAvailability', 'number', 
                array(
                    'label'    => 'Base Availability',
                    'required' => false,
                    'help'     =>"Only if destination is 'CUSTOM' destination",
                    )
                );
        }



        // ->add('minAvailability', 'number', 
        //     array(
        //         'label'    => 'Space Available',
        //         'required' => false,
        //         'help'     =>"Only if destination is not 'CUSTOM' destination",
        //         )
        //     )



        // if ($this->getSubject()->getId() && $this->getSubject()->getDestination()->getId() == 10) {
        //     // Es un EDITAR!
        //     $formMapper->add('custom_date', 'date', array(
        //         'required'  => false,
        //         'read_only' => true,
        //         'widget'    => 'single_text',
        //         )
        //     );
        // } else {
        //     $formMapper->add('custom_date', 'date', array(
        //         'required' => false,
        //         'widget'   => 'single_text'
        //         )
        //     );
        // }



        // if($this->getSubject()->getId() && !$this->getSubject()->getDate()){
        //     $date = new Carbon();
        //     $date->day += 5;
        //     $de = $this->getConfigurationPool()->getContainer()->get('Doctrine')->getRepository('AppBundle:TourDate')->findOneBy(array(
        //         'destination' => $this->getSubject()->getDestination(),
        //         'date' => $date,
        //         ));
        //     if($de) $this->getSubject()->setDate($de);
        // }

    }

    public function postPersist($category)
    {
        if ($category->getDestination()->getId() != 10) {
            return;
        }

        $manager = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();

        $fechaInicio = strtotime(Date('d') . "-" . Date('m') . "-" . $category->getYear());
        $fechaFin    = strtotime("31-12-" . $category->getYear());

        for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
            $tourDate = new TourDate();

            $tourDate->setDate( new \DateTime(date("d-m-Y", $i)) );
            $tourDate->setAvailability( null );
            $tourDate->setCategory( $category );
            
            $manager->persist( $tourDate );
        }

        $manager->flush();
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper->addIdentifier('id', 'string', array(
            'template' => 'admin/catLink.html.twig'
            ))->addIdentifier('name')->addIdentifier('destination');
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        if ($this->getSubject()->getDestination()->getId() == 10) {
            $errorElement
            ->with('baseAvailability')
            ->assertNotNull()
            ->assertNotBlank()
            ->assertLength(array('min' => 1))
            ->end();

            $errorElement
            ->with('year')
            ->assertNotNull()
            ->assertNotBlank()
            ->end();
        }
    }
}