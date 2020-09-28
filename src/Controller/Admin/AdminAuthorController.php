<?php

namespace App\Controller\Admin;

use App\Entity\Author;
use App\Form\AuthorType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAuthorController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/author", name="admin_author")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $data = $this->em->getRepository(Author::class)->findAll();

        $authors = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            50
        );

        return $this->render('admin/author/index.html.twig', [
            'authors' => $authors,
        ]);
    }

    /**
     * @Route("/admin/author/create", name="admin_create_author", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $author = new Author();

        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($author);
            $this->em->flush();

            return $this->redirectToRoute('admin_author');
        }

        return $this->render('admin/author/create.html.twig',
            [
                'form' => $form->createView(),
            ]);
    }

    /**
     * @Route("/admin/author/{id}/edit", name="admin_edit_author", methods={"GET","PUT"})
     */
    public function edit(Author $author, Request $request): Response
    {
        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($author);
            $this->em->flush();

            return $this->redirectToRoute('admin_author');
        }

        return $this->render('admin/author/edit.html.twig',
            [
                'form' => $form->createView(),
                'author' => $author,
            ]);
    }

    /**
     * @Route("/admin/author/{id}/delete", name="admin_delete_author")
     */
    public function delete($id): Response
    {
        $author = $this->em->getRepository(Author::class)->findOneBy(['id' => $id]);

        $this->em->remove($author);
        $this->em->flush();

        return $this->redirectToRoute('admin_author');
    }
}
