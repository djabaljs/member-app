<?php

namespace App\Form;

use App\Entity\Unit;
use App\Entity\Agent;
use App\Entity\Entity;
use App\Entity\Service;
use App\Entity\Direction;
use App\Entity\Department;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\Entity as EntityEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class AgentType extends AbstractType
{

    private $em;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->em = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => false,
                'required' => true
            ])
            ->add('lastName', TextType::class, [
                'label' => false,
                'required' => true
            ])
            ->add('post', TextType::class, [
                'label' => false,
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'required' => true
            ])
            ->add('fonction', TextType::class, [
                'label' => false,
                'required' => true
            ])
            ->add('entity', EntityType::class, [
                'label' => false,
                'class' => Entity::class,
            ])
            ->add('direction', EntityType::class, [
                'label' => false,
                'class' => Direction::class,
            ])
            ->add('department', EntityType::class, [
                'label' => false,
                'class' => Department::class,
            ])
            ->add('unit', EntityType::class, [
                'label' => false,
                'class' => Unit::class,
            ])
            ->add('fonction', EntityType::class, [
                'label' => false,
                'class' => Service::class,
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
        $unit = $entity->getUnit() ? $entity->getUnit() : null;
        
        $this->addElements($form, $unit);
    }
    
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Agent::class,
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
