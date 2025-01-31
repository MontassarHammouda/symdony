<?php

namespace App\Controller;

use App\Entity\Exercice;
use App\Form\ExerciceType;
use App\Repository\ExerciceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\UploaderHelper;

/**
 * @Route("/exercice")
 */
class ExerciceController extends AbstractController
{
    /**
     * @Route("/", name="exercice_index", methods={"GET"})
     */
    public function index(ExerciceRepository $exerciceRepository): Response
    {
        return $this->render('exercice/index.html.twig', [
            'exercices' => $exerciceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="exercice_new", methods={"GET","POST"})
     */
    public function new(Request $request, UploaderHelper $uploaderHelper): Response
    {
        $exercice = new Exercice();
        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['fileEx']->getData();
            if ($uploadedFile) {
                $newFilename = $uploaderHelper->uploadArticleImage($uploadedFile);
                $exercice->setFile($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($exercice);
            $entityManager->flush();

            return $this->redirectToRoute('exercice_index');
        }

        return $this->render('exercice/new.html.twig', [
            'exercice' => $exercice,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="exercice_show", methods={"GET"})
     */
    public function show(Exercice $exercice): Response
    {
        return $this->render('exercice/show.html.twig', [
            'exercice' => $exercice,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="exercice_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Exercice $exercice, UploaderHelper $uploaderHelper): Response
    {
        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['fileEx']->getData();
            if ($uploadedFile) {
                $newFilename = $uploaderHelper->uploadArticleImage($uploadedFile);
                $exercice->setFile($newFilename);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('exercice_index', [
                'id' => $exercice->getId(),
            ]);
        }

        return $this->render('exercice/edit.html.twig', [
            'exercice' => $exercice,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="exercice_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Exercice $exercice): Response
    {
        if ($this->isCsrfTokenValid('delete'.$exercice->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($exercice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('exercice_index');
    }
}
