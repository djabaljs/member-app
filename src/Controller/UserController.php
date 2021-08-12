<?php

namespace App\Controller;

use App\Entity\Unit;
use App\Entity\User;
use App\Entity\Agent;
use App\Entity\Service;
use App\Entity\Direction;
use App\Entity\Department;
use App\Entity\SpecificSearch;
use App\Entity\UtilNumber;
use App\Form\SpecificSearchType;
use App\Repository\AgentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{

  private $manager;

  public function __construct(EntityManagerInterface $entityManager)
  {
      $this->manager = $entityManager;
 
  }

    /**
     * @Route("/", name="user")
     */
    public function index(Request $request): Response
    {
        $specificSearch = new SpecificSearch();

        $form = $this->createForm(SpecificSearchType::class, $specificSearch);
        $form->handleRequest($request);

        return $this->render('frontend/index.html.twig', [
            'form' => $form->createView(),
            'utilNumbers' => $this->manager->getRepository(UtilNumber::class)->findAll()
        ]);
    }

     /**
     * @Route("/standard-search", name="standard_search")
     */
    public function standardSearch(Request $request, AgentRepository $agentRepository)
    {   
       if($request->get('search') != ''){
        $search = $request->get('search');
         $agents = $agentRepository->findAgentByStandardCriteria($search);
        
         $content = '';
         
         foreach($agents as $agent){
             $content .='<tr>';
                $content .= '<td>'.$agent->getFirstName().' '.$agent->getLastName().'</td>';
                $content .= '<td>'.$agent->getPost().'</td>';
                $content .= '<td>'.$agent->getEmail().'</td>';
                $content .= '<td>'.$agent->getFonction().'</td>';
                $content .= '<td><img type="button" id="agent-detail-'.$agent->getId().'" src="http://annuaire.presidence.ci/imgs/browsesmall.png"/>
                <!-- Modal -->
                <div id="modal_agent_'.$agent->getId().'" class="modal fade" role="dialog">
                  <div class="modal-dialog modal-lg">
                
                    <!-- Modal content-->
                    <div class="modal-content">
                      <div class="modal-header">
                        Détails sur '.$agent->getFirstName().' '.$agent->getLastName().'
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                      </div>
                      <div class="modal-body">
                      <div class="col-12">
                      <img class="img-fluid " src="http://annuaire.presidence.ci/imgs/header.jpg"/>
                      </div>
                      <div class="col-12">
                      <div class="card mb-3">
                      <div class="row g-0">
                        <div class="col-md-4">
                          <img src="http://annuaire.presidence.ci/imgs/user-default.jpeg" class="img-fluid rounded-start" alt="...">
                        </div>
                        <div class="col-md-8">
                          <div class="card-body">
                            <h3>'.$agent->getFirstName().' '.$agent->getLastName().'</h3>
                            <p><strong>Département:</strong> '.$agent->getFonction()->getDepartment()->getName().'</p>
                            <p><strong>Fonction:</strong> '.$agent->getFonction().'</p>
                            <p><strong>Email:</strong> '.$agent->getEmail().'</p>
                            <p><strong>N° Poste:</strong> '.$agent->getPost().'</p>
                          </div>
                        </div>
                      </div>
                     </div>
                      </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                      </div>
                    </div>
                
                  </div>
                </td>';

            $content .='<tr>';
             
         }

         return new JsonResponse(['content' => $content]);
       }

       return new Response('');
    }

         /**
     * @Route("/specific-search", name="specific_search")
     */
    public function specificSearch(Request $request, AgentRepository $agentRepository)
    {   
       if($request->get('search') != ''){
        $search = $request->get('search');
         $agents = $agentRepository->findAgentBySpecificCriteria($search);
        
         $content = '';
         
         foreach($agents as $agent){
             $content .='<tr>';
                $content .= '<td>'.$agent->getFirstName().' '.$agent->getLastName().'</td>';
                $content .= '<td>'.$agent->getPost().'</td>';
                $content .= '<td>'.$agent->getEmail().'</td>';
                $content .= '<td>'.$agent->getFonction().'</td>';
                $content .= '<td><img type="button" id="agent-detail-'.$agent->getId().'" src="http://annuaire.presidence.ci/imgs/browsesmall.png"/>
                <!-- Modal -->
                <div id="modal_agent_'.$agent->getId().'" class="modal fade" role="dialog">
                  <div class="modal-dialog modal-lg">
                
                    <!-- Modal content-->
                    <div class="modal-content">
                      <div class="modal-header">
                        Détails sur '.$agent->getFirstName().' '.$agent->getLastName().'
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                      </div>
                      <div class="modal-body">
                      <div class="col-12">
                        <img class="img-fluid " src="http://annuaire.presidence.ci/imgs/header.jpg"/>
                      </div>
                      <div class="col-12">
                      <div class="card mb-3">
                      <div class="row g-0">
                        <div class="col-md-4">
                          <img src="http://annuaire.presidence.ci/imgs/user-default.jpeg" class="img-fluid rounded-start" alt="...">
                        </div>
                        <div class="col-md-8">
                          <div class="card-body">
                            <h3>'.$agent->getFirstName().' '.$agent->getLastName().'</h3>
                            <p><strong>Département:</strong> '.$agent->getFonction()->getDepartment()->getName().'</p>
                            <p><strong>Fonction:</strong> '.$agent->getFonction().'</p>
                            <p><strong>Email:</strong> '.$agent->getEmail().'</p>
                            <p><strong>N° Poste:</strong> '.$agent->getPost().'</p>
                          </div>
                        </div>
                      </div>
                     </div>
                      </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                      </div>
                    </div>
                
                  </div>
                </td>';

            $content .='<tr>';
             
         }

         return new JsonResponse(['content' => $content]);
       }

       return new Response('');
    }

     /**
     * @Route("/verify-email/{email}", name="user-email")
     */
    public function verifyAgentEmail(Request $request)
    {
        // $user = $this->getDoctrine()->getRepository(Applicant::class)->findOneBy(['email' => $email]);
        $agent = $request->query->get('agent');

        $agentExist = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $agent['email']]);

        if ($agentExist) {
            return new JsonResponse(['Cet email est déjà associé à un autre utilisateur!']);
        } else {
            return new JsonResponse(['']);
        }
    }

    /**
     * @Route("/verify-post/", name="user_post")
     */
    public function verifyAgentPost(Request $request)
    {
        // $user = $this->getDoctrine()->getRepository(Applicant::class)->findOneBy(['email' => $email]);
        $post = $request->get('post');
  
        $agentExist = $this->getDoctrine()->getRepository(Agent::class)->findOneBy(['post' => $post]);

        if ($agentExist) {
            return new JsonResponse(['error' => 'Ce N° poste est déjà utilisé!']);
        } else {
            return new JsonResponse(['']);
        }
    }

        /**
     * Returns a JSON string with the neighborhoods of the City with the providen id.
     * 
     * @param Request $request
     * @return JsonResponse
     * @Route("/get-units-from-entity", name="get_units_from_entitys")
     */
    public function listUnitOfEntity(Request $request)
    {
        // Get Entity manager and repository
        $unitRepo = $this->manager->getRepository(Unit::class);
        // Search the neighborhoods that belongs to the city with the given id as GET parameter "cityid"
        $units = $unitRepo->createQueryBuilder("u")
            ->where("u.entity = :entityId")
            ->setParameter("entityId", $request->get("entityId"))
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
     * @Route("/get-directions-from-unit", name="get_directions_from_units")
     */
    public function listDirectionsOfUnit(Request $request)
    {
        // Get Entity manager and repository
        $directionRepo = $this->manager->getRepository(Direction::class);
        
        // Search the neighborhoods that belongs to the city with the given id as GET parameter "cityid"
        $directions = $directionRepo->createQueryBuilder("d")
            ->where("d.unit = :unitId")
            ->setParameter("unitId", $request->get("unitId"))
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
     * @Route("/get-departments-from-direction", name="get_departments_from_directions")
     */
    public function listDepartmentOfDirection(Request $request)
    {
        // Get Entity manager and repository
        $departmentRepo = $this->manager->getRepository(Department::class);
        
        // Search the neighborhoods that belongs to the city with the given id as GET parameter "cityid"
        $departments = $departmentRepo->createQueryBuilder("d")
            ->where("d.direction = :directionId")
            ->setParameter("directionId", $request->get("directionId"))
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
     * @Route("/get-services-from-department", name="get_services_from_departments")
     */
    public function listServicesOfDepartment(Request $request)
    {
        // Get Entity manager and repository
        $serviceRepo = $this->manager->getRepository(Service::class);
        
        // Search the neighborhoods that belongs to the city with the given id as GET parameter "cityid"
        $services = $serviceRepo->createQueryBuilder("s")
            ->where("s.department = :departmentId")
            ->setParameter("departmentId", $request->get("departmentId"))
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
