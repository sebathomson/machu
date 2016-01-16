<?php
namespace AppBundle\Admin;
 
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Route\RouteCollection;
 
class TourDateAdmin extends Admin {
 
    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper->add('date_read', 'text',
            array(
                'label'     => 'Date',
                'mapped'    => false,
                'read_only' => true,
                'data'      => $this->getSubject()->getDate()->format('d-m-Y'),
                )
            );
        $formMapper->add('availability', 'number');
    }
 
    protected function configureListFields(ListMapper $listMapper) {
        $listMapper->add('category', 'string');
        $listMapper->addIdentifier('date', 'string', 
            array(
                'template' => 'admin/TourDate/fieldDate.html.twig'
                )
            );
        $listMapper->addIdentifier('availability');
    }
 
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
        ->add('date', 'doctrine_orm_date')
        ->add('category', null, array(), 'entity', array(
            'class'    => 'AppBundle\Entity\Category',
            'property' => 'name',
            ))
        ;
    }
 
    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
        ->with('availability')
        ->assertNotNull()
        ->assertNotBlank()
        ->assertLength(array('min' => 1))
        ->end();
    }
 
    protected function configureRoutes(RouteCollection $collection)
    {
        // to remove a single route
        $collection->remove('delete');
        $collection->remove('create');
        // OR remove all route except named ones
        // $collection->clearExcept(array('list', 'show'));
    }
}