<?php

namespace App\Controller;

use App\Entity\Direction;
use App\Form\DirectionType;
use App\Repository\DirectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/direction")
 */
class DirectionController extends AbstractController
{
    /**
     * @Route("/", name="direction_index", methods={"GET"})
     */
    public function index(DirectionRepository $directionRepository): Response
    {
        return $this->render('direction/index.html.twig', [
            'directions' => $directionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="direction_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $direction = new Direction();
        $form = $this->createForm(DirectionType::class, $direction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($direction);
            $entityManager->flush();

            return $this->redirectToRoute('direction_index');
        }

        return $this->render('direction/new.html.twig', [
            'direction' => $direction,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="direction_show", methods={"GET"})
     */
    public function show(Direction $direction): Response
    {
        return $this->render('direction/show.html.twig', [
            'direction' => $direction,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="direction_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Direction $direction): Response
    {
        $form = $this->createForm(DirectionType::class, $direction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('direction_index');
        }

        return $this->render('direction/edit.html.twig', [
            'direction' => $direction,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="direction_delete", methods={"POST"})
     */
    public function delete(Request $request, Direction $direction): Response
    {
        if ($this->isCsrfTokenValid('delete'.$direction->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($direction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('direction_index');
    }
}
