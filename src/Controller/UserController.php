<?php

namespace App\Controller;

use App\Entity\Member;
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
        return $this->render('frontend/index.html.twig', []);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->get('matricule')) {
 
            try {
                $member = $this->manager->getRepository(Member::class)->findOneBy(['matricule' => $request->get('matricule')]);
                $row = '';
                $sessions = '';
                $posts = '';
                
                foreach ($member->getSessions() as $session) {
                    $sessions .= '<p>' . $session->getName() . '</p>';
                }
                foreach ($member->getPosts() as $post) {
                    $posts .= '<p>' . $post->getName() . '</p>';
                }

                $row .= '
                    
                        <div class="card">
                            <div class="card-media">
                              <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRX-NfjcF_sUqB0Yqy-NJ_YXhu62SQffp85Kg&usqp=CAU" alt="photo" />
                            </div>
                            <div class="card-content">
                             <h6>Nom : ' . $member->getFirstName() . '</h6>
                             <h6>Prénom : ' . $member->getLastName() . '</h6>
                             <h6>Date de naissance : ' . $member->getBirthDay() . '</h6>
                             <h6>Sessions : ' . $sessions . '</h6>
                             <h6>Postes : ' . $posts . '</h6>
                            </div>
                        </div>
                    ';

            return new JsonResponse(['row' => $row]);

            } catch (\Exception $e) {
                $row = '<h6 style="color: red">Matricule incorrecte</h6>';
                return new JsonResponse(['row' => $row]);
            }

        }

        return new Response(null);
    }
}
