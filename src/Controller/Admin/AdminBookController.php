<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/book", name="admin_book")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $data = $this->em->getRepository(Book::class)->findAll();

        $books = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            50
        );

        return $this->render('admin/book/index.html.twig', [
            'books' => $books,
        ]);
    }

    /**
     * @Route("/admin/book/create", name="admin_create_book")
     */
    public function create(Request $request): Response
    {
        $author = new Book();

        $form = $this->createForm(BookType::class, $author);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->em->persist($author);
            $this->em->flush();
            return $this->redirectToRoute('admin_book');
        }

        return $this->render('admin/book/create.html.twig',
            [
                'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/admin/book/{id}/edit", name="admin_edit_book")
     */
    public function edit(Book $book, Request $request): Response
    {
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->em->persist($book);
            $this->em->flush();
            return $this->redirectToRoute('admin_book');
        }

        return $this->render('admin/book/edit.html.twig',
            [
                'form' => $form->createView(),
                'book' => $book
            ]);
    }

    /**
     * @Route("/admin/book/{id}/delete", name="admin_delete_book")
     */
    public function delete($id): Response
    {
        $book = $this->em->getRepository(Book::class)->findOneBy(['id' => $id]);

        $this->em->remove($book);
        $this->em->flush();

        return $this->redirectToRoute('admin_book');
    }
}
