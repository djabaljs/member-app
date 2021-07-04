<?php

namespace App\Form;

use App\Entity\Unit;
use App\Entity\Entity;
use App\Entity\Service;
use App\Entity\Direction;
use App\Entity\Department;
use App\Entity\SpecificSearch;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecificSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('entity', EntityType::class, [
                'label' => false,
                'class' => Entity::class,
                'placeholder' => 'Selectionner une entité...'
            ])
            ->add('unit', EntityType::class, [
                'label' => false,
                'class' => Unit::class,
                'placeholder' => 'Selectionner une unité...'

            ])
            ->add('direction', EntityType::class, [
                'label' => false,
                'class' => Direction::class,
                'placeholder' => 'Selectionner une direction...'

            ])
            ->add('department', EntityType::class, [
                'label' => false,
                'class' => Department::class,
                'placeholder' => 'Selectionner un département...'

            ])
            ->add('fonction', EntityType::class, [
                'label' => false,
                'class' => Service::class,
                'placeholder' => 'Selectionner une fonction...'

            ])
        ;


           // 3. Add 2 event listeners for the form
           $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
           $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }  

    protected function addElements(FormInterface $form, Entity $entity = null)
    {
          // 4. Add the province element
          $form->add('entity', EntityType::class, array(
            'required' => true,
            'label' => false,
            'data' => $entity,
            'placeholder' => 'Selectionnner une entité...',
            'class' => Entity::class
        ));

         // untis empty, unless there is a selected City (Edit View)
         $units = array();
         $directions = array();
         $departments = array();
         $fonctions = array();

        if ($entity) {
            // Fetch Neighborhoods of the City if there's a selected city
            $repoUnit = $this->em->getRepository(Unit::class);
            
            $units = $repoUnit->createQueryBuilder("u")
                ->where("u.entity = :entityId")
                ->setParameter("entityId", $entity->getId())
                ->getQuery()
                ->getResult();
        }

        // Add the Unit field with the properly data
        $form->add('unit', EntityType::class, array(
            'required' => true,
            'placeholder' => 'Selectionner une unité...',
            'class' => Unit::class,
            'label' => false,
            'choices' => $units
        ));

          // Add the Unit field with the properly data
        $form->add('direction', EntityType::class, array(
            'required' => true,
            'placeholder' => 'Selectionner une direction...',
            'class' => Direction::class,
            'label' => false,
            'choices' => $directions
        ));

           // Add the Unit field with the properly data
        $form->add('department', EntityType::class, array(
            'required' => true,
            'placeholder' => 'Selectionner un département...',
            'class' => Department::class,
            'label' => false,
            'choices' => $departments
        ));

           // Add the Unit field with the properly data
           $form->add('fonction', EntityType::class, array(
            'required' => true,
            'placeholder' => 'Selectionner une fonction...',
            'class' => Service::class,
            'label' => false,
            'choices' => $fonctions
        ));
    }

    function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();
        
        // Search for selected City and convert it into an Entity
        $entity = $this->em->getRepository(Entity::class)->find($data['entity']);
        
        $this->addElements($form, $entity);
    }

    function onPreSetData(FormEvent $event) {
        $entity = $event->getData();
        $form = $event->getForm();

        // When you create a new person, the City is always empty
        $unit = $entity->unit? $entity->unit : null;
        
        $this->addElements($form, $unit);
    }
    

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SpecificSearch::class,
        ]);
    }

     /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return '';
    }
}
