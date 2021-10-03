<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\Post;
use App\Entity\Session;
use App\Entity\User;
use App\Form\MemberType;
use App\Form\PostType;
use App\Form\SessionType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/__manage__/admin")
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
        return $this->render('/backend/dashboard.html.twig', [
            'members' => $this->manager->getRepository(Member::class)->findAll(),
            'sessions' => $this->manager->getRepository(Session::class)->findAll(),
        ]);
    }

    /**
     * @Route("/members", name="members")
     */
    public function members(Request $request): Response
    {
        $member = new Member();
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

      
        if ($request->isXmlHttpRequest() && is_array($request->get('member'))) {

           
            $member->setFirstName($request->get('member')['firstName']);
            $member->setLastName($request->get('member')['lastName']);

            $member->setBirthDay($request->get('member')['birthDay']);

            $post = $this->manager->getRepository(Post::class)->find($request->get('member')['post']);

            $member->addPost($post);

            $session = $this->manager->getRepository(Session::class)->find($request->get('member')['session']);

            $member->addSession($session);

            $members = $this->manager->getRepository(Member::class)->findAll();

            if (count($members) <= 0) {
                $member->setMatricule('MEM-' . (count($members) + 1));
            } else {
                $member->setMatricule('MEM-' . count($members));
            }

            $member->setCreatedAt(new \DateTime());
      
            $this->manager->persist($member);
            $this->manager->flush();

            $sessions = $this->manager->getRepository(Session::class)->findAll();
            $posts = $this->manager->getRepository(Post::class)->findAll();

            $members = $this->manager->getRepository(Member::class)->findAll();

            $row = '';
            foreach ($members as $key => $member) {
                $option1 = "";
                $option2 = "";
                $memberPosts = '';
                $memberSessions = '';

                foreach ($sessions as $session) {

                    foreach ($session->getMembers() as $memberS) {
                        if ($session->getId() === $memberS->getId()) {
                            $option1 .= "<option value=" . $session->getId() . " selected>" . $session->getName() . "</option>";
                        } else {
                            $option1 .= "<option value=" . $session->getId() . ">" . $session->getName() . "</option>";
                        }
                    }
                }

                foreach ($posts as $post) {

                    foreach ($post->getMembers() as $memberP) {
                        if ($post->getId() === $memberP->getId()) {
                            $option2 .= "<option value=" . $post->getId() . " selected>" . $post->getName() . "</option>";
                        } else {
                            $option2 .= "<option value=" . $post->getId() . ">" . $post->getName() . "</option>";
                        }
                    }
                }

                foreach ($member->getPosts() as $postM) {
                    $memberPosts .= '<p>' . $postM->getName() . '</p>';
                }

                foreach ($member->getSessions() as $sessionM) {
                    $memberSessions .= '<p>' . $sessionM->getName() . '</p>';
                }


                $row .= '
           <tr id="tr-' . $member->getId() . '">
           <td>' . ($key + 1) . '</td>
           <td>' . $member->getFirstName() . '</td>
           <td>' . $member->getBirthDay() . '</td>
           <td>' . $member->getLastName() . '</td>
           <td>' . $memberSessions . '</td>
           <td>' . $memberPosts . '</td>
           <td >
               <a type="submit" id="btn-modify-' . $member->getId() . '"
                   class="btn btn-success btn-sm">Modifier <i class="fa fa-edit"></i></a>
               <a type="submit" id="delete-' . $member->getId() . '" class="btn btn-danger btn-sm"  data-toggle="modal"
                   data-target="#modal-danger">Supprimer 
                   <i class="fa fa-trash"></i></a>
                   <div class="modal fade" id="modal_delete_' . $member->getId() . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                   <div class="modal-dialog" role="document">
                       <div class="modal-content">
                           <div class="modal-header">
                               <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                               </a>
                           </div>
                           <div class="modal-body">
                               <p>Voulez-vous vraiment supprimer ' . $member->getFirstName() . ' ' . $member->getLastName() . ' ? Toutes les données liées à cette entité seront définitivement supprimées!</p>
                           </div>
                           <div class="modal-footer">
                               <a href="#" class="btn btn-secondary" data-dismiss="modal" style="color:#fff;">Annuler</a>
                               <a id="btn-delete-' . $member->getId() . '" class="btn btn-danger" style="color:#fff;">Supprimer</a>
                           </div>
                       </div>
                   </div>
               </div>
               <div id="modal_edit_' . $member->getId() . '" class="modal fade" id="form" tabindex="-1" role="dialog"
                   aria-labelledby="exampleModalLabel" aria-hidden="true">
                   <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                       <div class="modal-content">
                           <div class="modal-header border-bottom-0">
                               <h5 class="modal-title text-center" id="exampleModalLabel">Modifier un membre</h5>
                               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                               </button>
                           </div>
                           
                           <div class="modal-body">
                           <form id="edit_form_' . $member->getId() . '">
                               <div class="row">
                                   <div class="col-md-6 col-sm-6 col-xs-6">
                                       <div class="form-group">
                                           <label for="firstname">Nom</label>
                                           <input type="text" name="firstName" class="form-control" value="' . $member->getFirstName() . '">
                                       </div>
                                   </div>
                                   <div class="col-md-6 col-sm-6 col-xs-6">
                                       <div class="form-group">
                                           <label for="lastname">Prénom</label>
                                           <input type="text" name="lastName" class="form-control" value="' . $member->getLastName() . '">
                                       </div>
                                   </div>
                               </div>
                               <div class="row">
                                   <div class="col-md-12 col-sm-12 col-xs-12">
                                       <div class="form-group">
                                           <label for="birthDay">Date de naissance</label>
                                        <input type="date" name="birthDay" id="birthDay" class="form-control" value="' . $member->getBirthDay() . '">
                                       </div>
                                   </div>
                               </div>
                               <div class="row">
                                   <div class="col-md-6 col-sm-6 col-xs-6">
                                       <div class="form-group">
                                           <label for="session">Session</label>
                                            <select class="form-control" id="session" name="session">
                                                ' . $option1 . '
                                            </select>
                                       </div>
                                   </div>
                                   <div class="col-md-6 col-sm-6 col-xs-6">
                                       <div class="form-group">
                                           <label for="post">Poste</label>
                                             <select class="form-control" id="post" name="post">
                                            ' . $option2 . '
                                            </select>
                                       </div>
                                   </div>
                               </div>
                               <input type="hidden" name="member" value="' . $member->getId() . '">
                            </form>
                           </div>
                           <div class="modal-footer border-top-0 d-flex justify-content-center">
                               <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                               <button type="submit" id="edit-btn-' . $member->getId() . '" class="btn btn-warning">Modifier</button>
                           </div>
                       </div>
                   </div>
               </div>
           </td>
            </tr>
           ';
            }
            return new JsonResponse(['member' => $member->getId(), 'row' => $row]);
        } elseif ($request->isXmlHttpRequest() && $request->get('member')) {

            $member = $this->manager->getRepository(Member::class)->find($request->get('member'));
          
            $member->setFirstName($request->get('firstName'));
            $member->setLastName($request->get('lastName'));
            $member->setBirthDay($request->get('birthDay'));

            $post = $this->manager->getRepository(Post::class)->find($request->get('post'));


            $session = $this->manager->getRepository(Session::class)->find($request->get('session'));


            $members = $this->manager->getRepository(Member::class)->findAll();

            $member->setMatricule('MEM' . count($members));
      
            $this->manager->persist($member);
            $this->manager->flush();

            $sessions = $this->manager->getRepository(Session::class)->findAll();
            $posts = $this->manager->getRepository(Post::class)->findAll();


            $row = '';
            foreach ($members as $key => $member) {
                $option1 = "";
                $option2 = "";
                $memberPosts = '';
                $memberSessions = '';
              
                foreach ($sessions as $session) {

                    foreach ($session->getMembers() as $memberS) {
                        if ($session->getId() === $memberS->getId()) {
                            $option1 .= "<option value=" . $session->getId() . " selected>" . $session->getName() . "</option>";
                        } else {
                            $option1 .= "<option value=" . $session->getId() . ">" . $session->getName() . "</option>";
                        }
                    }
                }

                foreach ($posts as $post) {

                    foreach ($post->getMembers() as $memberP) {
                        if ($post->getId() === $memberP->getId()) {
                            $option2 .= "<option value=" . $post->getId() . " selected>" . $post->getName() . "</option>";
                        } else {
                            $option2 .= "<option value=" . $post->getId() . ">" . $post->getName() . "</option>";
                        }
                    }
                }

                foreach ($member->getPosts() as $postM) {
                    $memberPosts .= '<p>' . $postM->getName() . '</p>';
                }

                foreach ($member->getSessions() as $sessionM) {
                    $memberSessions .= '<p>' . $sessionM->getName() . '</p>';
                }
                $row .= '
           <tr id="tr-' . $member->getId() . '">
           <td>' . ($key + 1) . '</td>
           <td>' . $member->getFirstName() . '</td>
           <td>' . $member->getLastName() . '</td>
           <td>' . $member->getBirthDay() . '</td>
           <td>' . $memberSessions . '</td>
           <td>' . $memberPosts . '</td>
           <td >
               <a type="submit" id="btn-modify-' . $member->getId() . '"
                   class="btn btn-success btn-sm">Modifier <i class="fa fa-edit"></i></a>
               <a type="submit" id="delete-' . $member->getId() . '" class="btn btn-danger btn-sm"  data-toggle="modal"
                   data-target="#modal-danger">Supprimer 
                   <i class="fa fa-trash"></i></a>
                   <div class="modal fade" id="modal_delete_' . $member->getId() . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                   <div class="modal-dialog" role="document">
                       <div class="modal-content">
                           <div class="modal-header">
                               <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                               </a>
                           </div>
                           <div class="modal-body">
                               <p>Voulez-vous vraiment supprimer ' . $member->getFirstName() . ' ' . $member->getLastName() . ' ? Toutes les données liées à cette entité seront définitivement supprimées!</p>
                           </div>
                           <div class="modal-footer">
                               <a href="#" class="btn btn-secondary" data-dismiss="modal" style="color:#fff;">Annuler</a>
                               <a id="btn-delete-' . $member->getId() . '" class="btn btn-danger" style="color:#fff;">Supprimer</a>
                           </div>
                       </div>
                   </div>
               </div>
               <div id="modal_edit_' . $member->getId() . '" class="modal fade" id="form" tabindex="-1" role="dialog"
                   aria-labelledby="exampleModalLabel" aria-hidden="true">
                   <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                       <div class="modal-content">
                           <div class="modal-header border-bottom-0">
                               <h5 class="modal-title text-center" id="exampleModalLabel">Modifier un membre</h5>
                               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                               </button>
                           </div>
                           
                           <div class="modal-body">
                           <form id="edit_form_' . $member->getId() . '">
                               <div class="row">
                                   <div class="col-md-6 col-sm-6 col-xs-6">
                                       <div class="form-group">
                                           <label for="firstname">Nom</label>
                                           <input type="text" name="firstName" class="form-control" value="' . $member->getFirstName() . '">
                                       </div>
                                   </div>
                                   <div class="col-md-6 col-sm-6 col-xs-6">
                                       <div class="form-group">
                                           <label for="lastname">Prénom</label>
                                           <input type="text" name="lastName" class="form-control" value="' . $member->getLastName() . '">
                                       </div>
                                   </div>
                               </div>
                               <div class="row">
                                   <div class="col-md-12 col-sm-12 col-xs-12">
                                       <div class="form-group">
                                           <label for="birthDay">Date de naissance</label>
                                        <input type="date" name="birthDay" id="birthDay" class="form-control" value="' . $member->getBirthDay() . '">
                                       </div>
                                   </div>
                               </div>
                               <div class="row">
                                   <div class="col-md-6 col-sm-6 col-xs-6">
                                       <div class="form-group">
                                           <label for="session">Session</label>
                                            <select class="form-control" id="session" name="session">
                                                ' . $option1 . '
                                            </select>
                                       </div>
                                   </div>
                                   <div class="col-md-6 col-sm-6 col-xs-6">
                                       <div class="form-group">
                                           <label for="post">Poste</label>
                                             <select class="form-control" id="post" name="post">
                                            ' . $option2 . '
                                            </select>
                                       </div>
                                   </div>
                               </div>
                               <input type="hidden" name="member" value="' . $member->getId() . '">
                            </form>
                           </div>
                           <div class="modal-footer border-top-0 d-flex justify-content-center">
                               <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                               <button type="submit" id="edit-btn-' . $member->getId() . '" class="btn btn-warning">Modifier</button>
                           </div>
                       </div>
                   </div>
               </div>
           </td>
            </tr>
           ';
            }
            return new JsonResponse(['member' => $member->getId(), 'row' => $row]);
        } elseif ($request->isXmlHttpRequest()) {
        }

        $member = new Member();
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);


        return $this->render('backend/members/index.html.twig', [
            'members' => $this->manager->getRepository(Member::class)->findBy([], ['id' => 'DESC']),
            'form' => $form->createView(),
            'sessions' => $this->manager->getRepository(Session::class)->findBy([], ['id' => 'DESC']),
            'posts' => $this->manager->getRepository(Post::class)->findBy([], ['id' => 'DESC']),
        ]);
    }

    /**
     * @Route("/members/delete", name="member_remove")
     */
    public function agentRemove(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->get('member')) {
            $member = $this->manager->getRepository(Member::class)->find($request->get('member'));

            if ($member->getId()) {

                $id = $member->getId();

                $this->manager->remove($member);
                $this->manager->flush();

                return new JsonResponse(['member' => $id]);
            }
        }

        return null;
    }

    /**
     * 
     * @Route("/sessions", name="sessions")
     */
    public function sessions(Request $request): Response
    {
        $session = new Session();
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($request->isXmlHttpRequest() && $session->getName()) {

            $session->setCreatedAt(new \DateTime());
            $this->manager->persist($session);
            $this->manager->flush();

            $sessions = $this->manager->getRepository(Session::class)->findBy([], ['id' => 'DESC']);

            $row = '';
            foreach ($sessions as $key => $session) {
                $row .= '
           <tr id="tr-' . $session->getId() . '">
           <td>' . ($key + 1) . '</td>
           <td id="td-' . $session->getId() . '">' . $session->getName() . '</td>
           <td >
               <a type="submit" id="btn-modify-' . $session->getId() . '"
                   class="btn btn-success btn-sm">Modifier <i class="fa fa-edit"></i></a>
               <a type="submit" id="delete-' . $session->getId() . '" class="btn btn-danger btn-sm"  data-toggle="modal"
                   data-target="#modal-danger">Supprimer 
                   <i class="fa fa-trash"></i></a>
                   <div class="modal fade" id="modal_delete_' . $session->getId() . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                   <div class="modal-dialog" role="document">
                       <div class="modal-content">
                           <div class="modal-header">
                               <h5 class="modal-title text-uppercase" style="color:#ffff;" >' . $session->getName() . '</h5>
                               <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                               </a>
                           </div>
                           <div class="modal-body">
                               <p>Voulez-vous vraiment supprimer ' . $session->getName() . '? Toutes les données liées à cette session seront définitivement supprimées!</p>
                           </div>
                           <div class="modal-footer">
                               <a href="#" class="btn btn-secondary" data-dismiss="modal" style="color:#fff;">Annuler</a>
                               <a id="btn-delete-' . $session->getId() . '" class="btn btn-danger" style="color:#fff;">Supprimer</a>
                           </div>
                       </div>
                   </div>
               </div>
               <div id="modal_edit_' . $session->getId() . '" class="modal fade" id="form" tabindex="-1" role="dialog"
                   aria-labelledby="exampleModalLabel" aria-hidden="true">
                   <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                       <div class="modal-content">
                           <div class="modal-header border-bottom-0">
                               <h5 class="modal-title text-center" id="exampleModalLabel">Modifier une session</h5>
                               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                               </button>
                           </div>
                           
                           <div class="modal-body">
                           <form id="edit_form_' . $session->getId() . '" action="">
                               <div class="row">
                                   <div class="col-md-12 col-sm-12 col-xs-12">
                                       <div class="form-group">
                                           <label for="name">Nom</label>
                                           <input type="text" name="name" id="name-' . $session->getId() . '" class="form-control" value="' . $session->getName() . '">
                                       </div>
                                   </div>
                               </div>
                               <input type="hidden" name="session" value="' . $session->getId() . '">
                            </form>
                           </div>
                           <div class="modal-footer border-top-0 d-flex justify-content-center">
                               <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                               <button type="submit" id="edit-btn-' . $session->getId() . '" class="btn btn-warning">Modifier</button>
                           </div>
                       </div>
                   </div>
               </div>
           </td>
            </tr>
           ';
            }
            return new JsonResponse(['session' => $session->getId(), 'row' => $row]);
        } elseif ($request->isXmlHttpRequest() && $request->get('session')) {

            $session = $this->manager->getRepository(Session::class)->find($request->get('session'));

            $session->setName($request->get('name'));

            $this->manager->persist($session);
            $this->manager->flush();

            return new JsonResponse(['session' => $session->getId(), 'name' => $session->getName()]);
        } elseif ($request->isXmlHttpRequest()) {
        }


        return $this->render('backend/sessions/index.html.twig', [
            'sessions' => $this->manager->getRepository(Session::class)->findBy([], ['id' => 'DESC']),
            'form' => $form->createView(),
        ]);
    }

    /**
     * 
     * @Route("/sessions/delete", name="session_remove")
     */
    public function sessionsRemove(Request $request)
    {

        if ($request->isXmlHttpRequest() && $request->get('session')) {
            $session = $this->manager->getRepository(Session::class)->find($request->get('session'));

            if ($session->getId()) {

                $id = $session->getId();

                $this->manager->remove($session);
                $this->manager->flush();

                return new JsonResponse(['session' => $id]);
            }
        }

        return null;
    }




    /**
     * 
     * @Route("/posts", name="posts")
     */
    public function posts(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($request->isXmlHttpRequest() && $post->getName()) {

            $post->setCreatedAt(new \DateTime());
            $this->manager->persist($post);
            $this->manager->flush();

            $posts = $this->manager->getRepository(Post::class)->findBy([], ['id' => 'DESC']);

            $row = '';
            foreach ($posts as $key => $post) {
                $row .= '
           <tr id="tr-' . $post->getId() . '">
           <td>' . ($key + 1) . '</td>
           <td id="td-' . $post->getId() . '">' . $post->getName() . '</td>
           <td >
               <a type="submit" id="btn-modify-' . $post->getId() . '"
                   class="btn btn-success btn-sm">Modifier <i class="fa fa-edit"></i></a>
               <a type="submit" id="delete-' . $post->getId() . '" class="btn btn-danger btn-sm"  data-toggle="modal"
                   data-target="#modal-danger">Supprimer 
                   <i class="fa fa-trash"></i></a>
                   <div class="modal fade" id="modal_delete_' . $post->getId() . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                   <div class="modal-dialog" role="document">
                       <div class="modal-content">
                           <div class="modal-header">
                               <h5 class="modal-title text-uppercase" style="color:#ffff;" >' . $post->getName() . '</h5>
                               <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                               </a>
                           </div>
                           <div class="modal-body">
                               <p>Voulez-vous vraiment supprimer ' . $post->getName() . '? Toutes les données liées à ce poste seront définitivement supprimées!</p>
                           </div>
                           <div class="modal-footer">
                               <a href="#" class="btn btn-secondary" data-dismiss="modal" style="color:#fff;">Annuler</a>
                               <a id="btn-delete-' . $post->getId() . '" class="btn btn-danger" style="color:#fff;">Supprimer</a>
                           </div>
                       </div>
                   </div>
               </div>
               <div id="modal_edit_' . $post->getId() . '" class="modal fade" id="form" tabindex="-1" role="dialog"
                   aria-labelledby="exampleModalLabel" aria-hidden="true">
                   <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                       <div class="modal-content">
                           <div class="modal-header border-bottom-0">
                               <h5 class="modal-title text-center" id="exampleModalLabel">Modifier une post</h5>
                               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                               </button>
                           </div>
                           
                           <div class="modal-body">
                           <form id="edit_form_' . $post->getId() . '" action="">
                               <div class="row">
                                   <div class="col-md-12 col-sm-12 col-xs-12">
                                       <div class="form-group">
                                           <label for="name">Nom</label>
                                           <input type="text" name="name" id="name-' . $post->getId() . '" class="form-control" value="' . $post->getName() . '">
                                       </div>
                                   </div>
                               </div>
                               <input type="hidden" name="post" value="' . $post->getId() . '">
                            </form>
                           </div>
                           <div class="modal-footer border-top-0 d-flex justify-content-center">
                               <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                               <button type="submit" id="edit-btn-' . $post->getId() . '" class="btn btn-warning">Modifier</button>
                           </div>
                       </div>
                   </div>
               </div>
           </td>
            </tr>
           ';
            }
            return new JsonResponse(['post' => $post->getId(), 'row' => $row]);
        } elseif ($request->isXmlHttpRequest() && $request->get('post')) {

            $post = $this->manager->getRepository(Post::class)->find($request->get('post'));

            $post->setName($request->get('name'));

            $this->manager->persist($post);
            $this->manager->flush();

            return new JsonResponse(['post' => $post->getId(), 'name' => $post->getName()]);
        } elseif ($request->isXmlHttpRequest()) {
        }


        return $this->render('backend/posts/index.html.twig', [
            'posts' => $this->manager->getRepository(Post::class)->findBy([], ['id' => 'DESC']),
            'form' => $form->createView(),
        ]);
    }

    /**
     * 
     * @Route("/posts/delete", name="post_remove")
     */
    public function postsRemove(Request $request)
    {

        if ($request->isXmlHttpRequest() && $request->get('post')) {
            $post = $this->manager->getRepository(Post::class)->find($request->get('post'));

            if ($post->getId()) {

                $id = $post->getId();

                $this->manager->remove($post);
                $this->manager->flush();

                return new JsonResponse(['post' => $id]);
            }
        }

        return null;
    }

    /**
     * @Route("/administrators", name="administrators")
     */
    public function administrators(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($request->isXmlHttpRequest() && !$request->get('userId')) {

            $emailExist = $this->manager->getRepository(User::class)->findOneBy(['email' => $request->get('user')['email']]);


            if ($emailExist) {
                return new JsonResponse(['emailExist' => true]);
            }

            $user->setFirstName($request->get('user')['firstName']);
            $user->setLastName($request->get('user')['lastName']);
            $password = $encoder->encodePassword($user, '123456');
            $user->setEmail($request->get('user')['email']);
            $user->setPassword($password);

            $this->manager->persist($user);
            $this->manager->flush();

            $users = $this->manager->getRepository(User::class)->findBy([], ['id' => 'DESC']);

            $row = '';
            foreach ($users as $key => $user) {

                $row .= '
           <tr id="tr-' . $user->getId() . '">
           <td>' . ($key + 1) . '</td>
           <td>' . $user->getFirstName() . '</td>
           <td>' . $user->getLastName() . '</td>
           <td>' . $user->getEmail() . '</td>
           <td >
               <a type="submit" id="btn-modify-' . $user->getId() . '"
                   class="btn btn-success btn-sm">Modifier <i class="fa fa-edit"></i></a>
               <a type="submit" id="delete-' . $user->getId() . '" class="btn btn-danger btn-sm"  data-toggle="modal"
                   data-target="#modal-danger">Supprimer 
                   <i class="fa fa-trash"></i></a>
                   <div class="modal fade" id="modal_delete_' . $user->getId() . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                   <div class="modal-dialog" role="document">
                       <div class="modal-content">
                           <div class="modal-header">
                               <h5 class="modal-title text-uppercase" style="color:#ffff;" >' . $user->getFirstName() . ' ' . $user->getLastName() . '</h5>
                               <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                               </a>
                           </div>
                           <div class="modal-body">
                               <p>Voulez-vous vraiment supprimer ' . $user->getFirstName() . ' ' . $user->getLastName() . '? Toutes les données liées à cette entité seront définitivement supprimées!</p>
                           </div>
                           <div class="modal-footer">
                               <a href="#" class="btn btn-secondary" data-dismiss="modal" style="color:#fff;">Annuler</a>
                               <a id="btn-delete-' . $user->getId() . '" class="btn btn-danger" style="color:#fff;">Supprimer</a>
                           </div>
                       </div>
                   </div>
               </div>
               <div id="modal_edit_' . $user->getId() . '" class="modal fade" id="form" tabindex="-1" role="dialog"
                   aria-labelledby="exampleModalLabel" aria-hidden="true">
                   <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                       <div class="modal-content">
                           <div class="modal-header border-bottom-0">
                               <h5 class="modal-title text-center" id="exampleModalLabel">Modifier une entité</h5>
                               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                               </button>
                           </div>
                           
                           <div class="modal-body">
                           <form id="edit_form_' . $user->getId() . '">
                               <div class="row">
                                   <div class="col-md-6 col-sm-6 col-xs-6">
                                       <div class="form-group">
                                           <label for="firstname">Nom</label>
                                           <input type="text" name="firstName" class="form-control" value="' . $user->getFirstName() . '">
                                       </div>
                                   </div>
                                   <div class="col-md-6 col-sm-6 col-xs-6">
                                       <div class="form-group">
                                           <label for="lastname">Prénom</label>
                                           <input type="text" name="lastName" class="form-control" value="' . $user->getLastName() . '">
                                       </div>
                                   </div>
                               </div>
                               <div class="row">
                                   <div class="col-md-6 col-sm-6 col-xs-6">
                                       <div class="form-group">
                                           <label for="email">Email</label>
                                        <input type="text" name="email" class="form-control" value="' . $user->getEmail() . '">
                                       </div>
                                   </div>
                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                       <div class="form-group">
                                           <label for="password">Mot de passe</label>
                                        <input type="password" name="password" class="form-control" value="">
                                       </div>
                                   </div>
                               </div>
                               <input type="hidden" name="agent" value="' . $user->getId() . '">
                            </form>
                           </div>
                           <div class="modal-footer border-top-0 d-flex justify-content-center">
                               <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                               <button type="submit" id="edit-btn-' . $user->getId() . '" class="btn btn-warning">Modifier</button>
                           </div>
                       </div>
                   </div>
               </div>
           </td>
            </tr>
           ';
            }
            return new JsonResponse(['user' => $user->getId(), 'row' => $row]);
        } elseif ($request->isXmlHttpRequest() && $request->get('userId')) {

            $user = $this->manager->getRepository(User::class)->find($request->get('userId'));

            $user->setFirstName($request->get('firstName'));
            $user->setLastName($request->get('lastName'));
            $user->setEmail($request->get('email'));

            $password = $encoder->encodePassword($user, $request->get('password'));

            $user->setEmail($request->get('email'));
            $user->setPassword($password);

            $this->manager->persist($user);
            $this->manager->flush();

            $users = $this->manager->getRepository(User::class)->findBy([], ['id' => 'DESC']);

            $row = '';
            foreach ($users as $key => $user) {

                $row .= '
               <tr id="tr-' . $user->getId() . '">
               <td>' . ($key + 1) . '</td>
               <td>' . $user->getFirstName() . '</td>
               <td>' . $user->getLastName() . '</td>
               <td>' . $user->getEmail() . '</td>
               <td >
                   <a type="submit" id="btn-modify-' . $user->getId() . '"
                       class="btn btn-success btn-sm">Modifier <i class="fa fa-edit"></i></a>
                   <a type="submit" id="delete-' . $user->getId() . '" class="btn btn-danger btn-sm"  data-toggle="modal"
                       data-target="#modal-danger">Supprimer 
                       <i class="fa fa-trash"></i></a>
                       <div class="modal fade" id="modal_delete_' . $user->getId() . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                       <div class="modal-dialog" role="document">
                           <div class="modal-content">
                               <div class="modal-header">
                                   <h5 class="modal-title text-uppercase" style="color:#ffff;" >' . $user->getFirstName() . ' ' . $user->getLastName() . '</h5>
                                   <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                       <span aria-hidden="true">&times;</span>
                                   </a>
                               </div>
                               <div class="modal-body">
                                   <p>Voulez-vous vraiment supprimer ' . $user->getFirstName() . ' ' . $user->getLastName() . '? Toutes les données liées à cette entité seront définitivement supprimées!</p>
                               </div>
                               <div class="modal-footer">
                                   <a href="#" class="btn btn-secondary" data-dismiss="modal" style="color:#fff;">Annuler</a>
                                   <a id="btn-delete-' . $user->getId() . '" class="btn btn-danger" style="color:#fff;">Supprimer</a>
                               </div>
                           </div>
                       </div>
                   </div>
                   <div id="modal_edit_' . $user->getId() . '" class="modal fade" id="form" tabindex="-1" role="dialog"
                       aria-labelledby="exampleModalLabel" aria-hidden="true">
                       <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                           <div class="modal-content">
                               <div class="modal-header border-bottom-0">
                                   <h5 class="modal-title text-center" id="exampleModalLabel">Modifier une entité</h5>
                                   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                       <span aria-hidden="true">&times;</span>
                                   </button>
                               </div>
                               
                               <div class="modal-body">
                               <form id="edit_form_' . $user->getId() . '">
                                   <div class="row">
                                       <div class="col-md-6 col-sm-6 col-xs-6">
                                           <div class="form-group">
                                               <label for="firstname">Nom</label>
                                               <input type="text" name="firstName" class="form-control" value="' . $user->getFirstName() . '">
                                           </div>
                                       </div>
                                       <div class="col-md-6 col-sm-6 col-xs-6">
                                           <div class="form-group">
                                               <label for="lastname">Prénom</label>
                                               <input type="text" name="lastName" class="form-control" value="' . $user->getLastName() . '">
                                           </div>
                                       </div>
                                   </div>
                                   <div class="row">
                                       <div class="col-md-6 col-sm-6 col-xs-6">
                                           <div class="form-group">
                                               <label for="email">Email</label>
                                            <input type="text" name="email" class="form-control" value="' . $user->getEmail() . '">
                                           </div>
                                       </div>
                                       <div class="col-md-6 col-sm-6 col-xs-6">
                                       <div class="form-group">
                                           <label for="password">Mot de passe</label>
                                        <input type="password" name="password" class="form-control" value="">
                                       </div>
                                   </div>
                                   </div>
                                   <input type="hidden" name="userId" value="' . $user->getId() . '">
                                </form>
                               </div>
                               <div class="modal-footer border-top-0 d-flex justify-content-center">
                                   <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                   <button type="submit" id="edit-btn-' . $user->getId() . '" class="btn btn-warning">Modifier</button>
                               </div>
                           </div>
                       </div>
                   </div>
               </td>
                </tr>
               ';
            }
            return new JsonResponse(['user' => $user->getId(), 'row' => $row]);
        }

        return $this->render('backend/administrators/index.html.twig', [
            'users' => $this->manager->getRepository(User::class)->findBy([], ['id' => 'DESC']),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/administrators/delete", name="administrators_remove")
     */
    public function administratorRemove(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->get('userId')) {
            $user = $this->manager->getRepository(User::class)->find($request->get('userId'));

            if ($user->getId()) {

                $id = $user->getId();

                $this->manager->remove($user);
                $this->manager->flush();

                return new JsonResponse(['user' => $id]);
            }
        }

        return new Response('');
    }
}
