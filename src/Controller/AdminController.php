<?php

namespace App\Controller;

use App\Entity\Unit;
use App\Entity\Agent;
use App\Entity\Entity;
use App\Entity\Service;
use App\Form\AgentType;
use App\Entity\Direction;
use App\Entity\Department;
use App\Form\AgentTypeEdit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/presidence/admin/phone-book")
 */
class AdminController extends AbstractController
{

    private $manager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->manager = $entityManager;
    }

    /**
     * @Route("/", name="dashboard")
     */
    public function index(): Response
    {
        return $this->render('/backend/dashboard.html.twig');
    }

    /**
     * @Route("/agents", name="agents")
     */
    public function agents(Request $request): Response
    {   
        $agent = new Agent();
        $form = $this->createForm(AgentType::class, $agent);
        $form->handleRequest($request);


        if($request->isXmlHttpRequest()){
          if($request->get('firstName')){
                
           $agent = new Agent();
   
           $agent->setFirstName($request->get('firstName'));
           $agent->setLastName($request->get('lastName'));
           $agent->setEmail($request->get('email'));
           $agent->setPost($request->get('post'));
        
           $entity = $this->manager->getRepository(Entity::class)->find($request->get('entity'));
           $unit = $this->manager->getRepository(Unit::class)->find($request->get('unit'));
           $direction = $this->manager->getRepository(Direction::class)->find($request->get('direction'));
           $department = $this->manager->getRepository(Department::class)->find($request->get('department'));
           $fonction = $this->manager->getRepository(Service::class)->find($request->get('fonction'));

           $agent->setEntity($entity);
           $agent->setUnit($unit);
           $agent->setDirection($direction);
           $agent->setDepartment($department);
           $agent->setFonction($fonction);

           $this->manager->persist($agent);
           $this->manager->flush();

           return new JsonResponse(['agent' => $agent->getId()]);
          }
        }

        if( $form->isSubmitted() && $form->isValid()){
            $this->manager->persist($agent);
            $this->manger->flush();

            $this->addFlash('success', 'L\'agent a été enregistré avec succès !');

            $this->redirectToRoute('agents');
        }
        
        return $this->render('backend/agents/index.html.twig', [
            'agents' => $this->manager->getRepository(Agent::class)->findBy([], ['id' => 'DESC']),
            'form' => $form->createView(),
            'entities' => $this->manager->getRepository(Entity::class)->findBy([], ['id' => 'DESC']),
            'units' => $this->manager->getRepository(Unit::class)->findBy([], ['id' => 'DESC']),
            'directions' => $this->manager->getRepository(Direction::class)->findBy([], ['id' => 'DESC']),
            'departments' => $this->manager->getRepository(Department::class)->findBy([], ['id' => 'DESC']),
            'fonctions' => $this->manager->getRepository(Service::class)->findBy([], ['id' => 'DESC'])
        ]);
    }


    
    /**
     * Returns a JSON string with the neighborhoods of the City with the providen id.
     * 
     * @param Request $request
     * @return JsonResponse
     * @Route("/get-units-from-entity", name="get_units_from_entity")
     */
    public function listUnitOfEntity(Request $request)
    {
        // Get Entity manager and repository
        $unitRepo = $this->manager->getRepository(Unit::class);
        
        // Search the neighborhoods that belongs to the city with the given id as GET parameter "cityid"
        $units = $unitRepo->createQueryBuilder("u")
            ->where("u.entity = :entityId")
            ->setParameter("entityId", $request->query->get("entityId"))
            ->getQuery()
            ->getResult();
        
        // Serialize into an array the data that we need, in this case only name and id
        // Note: you can use a serializer as well, for explanation purposes, we'll do it manually
        $responseArray = array();
        foreach($units as $unit){
            $responseArray[] = array(
                "id" => $unit->getId(),
                "name" => $unit->getName()
            );
        }
        
        // Return array with structure of the neighborhoods of the providen city id
        return new JsonResponse($responseArray);

        // e.g
        // [{"id":"3","name":"Treasure Island"},{"id":"4","name":"Presidio of San Francisco"}]
    }

      /**
     * Returns a JSON string with the neighborhoods of the City with the providen id.
     * 
     * @param Request $request
     * @return JsonResponse
     * @Route("/get-directions-from-unit", name="get_directions_from_unit")
     */
    public function listDirectionsOfUnit(Request $request)
    {
        // Get Entity manager and repository
        $directionRepo = $this->manager->getRepository(Direction::class);
        
        // Search the neighborhoods that belongs to the city with the given id as GET parameter "cityid"
        $directions = $directionRepo->createQueryBuilder("d")
            ->where("d.unit = :unitId")
            ->setParameter("unitId", $request->query->get("unitId"))
            ->getQuery()
            ->getResult();
        
        // Serialize into an array the data that we need, in this case only name and id
        // Note: you can use a serializer as well, for explanation purposes, we'll do it manually
        $responseArray = array();
        foreach($directions as $direction){
            $responseArray[] = array(
                "id" => $direction->getId(),
                "name" => $direction->getName()
            );
        }
        
        // Return array with structure of the neighborhoods of the providen city id
        return new JsonResponse($responseArray);

        // e.g
        // [{"id":"3","name":"Treasure Island"},{"id":"4","name":"Presidio of San Francisco"}]
    }

      /**
     * Returns a JSON string with the neighborhoods of the City with the providen id.
     * 
     * @param Request $request
     * @return JsonResponse
     * @Route("/get-departments-from-direction", name="get_departments_from_direction")
     */
    public function listDepartmentOfDirection(Request $request)
    {
        // Get Entity manager and repository
        $departmentRepo = $this->manager->getRepository(Department::class);
        
        // Search the neighborhoods that belongs to the city with the given id as GET parameter "cityid"
        $departments = $departmentRepo->createQueryBuilder("d")
            ->where("d.direction = :directionId")
            ->setParameter("directionId", $request->query->get("directionId"))
            ->getQuery()
            ->getResult();
        
        // Serialize into an array the data that we need, in this case only name and id
        // Note: you can use a serializer as well, for explanation purposes, we'll do it manually
        $responseArray = array();
        foreach($departments as $department){
            $responseArray[] = array(
                "id" => $department->getId(),
                "name" => $department->getName()
            );
        }
        
        // Return array with structure of the neighborhoods of the providen city id
        return new JsonResponse($responseArray);

        // e.g
        // [{"id":"3","name":"Treasure Island"},{"id":"4","name":"Presidio of San Francisco"}]
    }

     /**
     * Returns a JSON string with the neighborhoods of the City with the providen id.
     * 
     * @param Request $request
     * @return JsonResponse
     * @Route("/get-services-from-department", name="get_services_from_department")
     */
    public function listServicesOfDepartment(Request $request)
    {
        // Get Entity manager and repository
        $serviceRepo = $this->manager->getRepository(Service::class);
        
        // Search the neighborhoods that belongs to the city with the given id as GET parameter "cityid"
        $services = $serviceRepo->createQueryBuilder("s")
            ->where("s.department = :departmentId")
            ->setParameter("departmentId", $request->query->get("departmentId"))
            ->getQuery()
            ->getResult();
        
        // Serialize into an array the data that we need, in this case only name and id
        // Note: you can use a serializer as well, for explanation purposes, we'll do it manually
        $responseArray = array();
        foreach($services as $service){
            $responseArray[] = array(
                "id" => $service->getId(),
                "name" => $service->getName()
            );
        }
        
        // Return array with structure of the neighborhoods of the providen city id
        return new JsonResponse($responseArray);

        // e.g
        // [{"id":"3","name":"Treasure Island"},{"id":"4","name":"Presidio of San Francisco"}]
    }
}
