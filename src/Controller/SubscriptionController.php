<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\SubscriptionFormType;
use App\Service\Subscriptions\AddSubscriptionService;
use App\Service\Subscriptions\GetUserSubscriptionService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    /**
     * @var GetUserSubscriptionService
     */
    private $userSubscriptionService;
    /**
     * @var AddSubscriptionService
     */
    private $addSubscriptionService;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em, GetUserSubscriptionService $userSubscriptionService, AddSubscriptionService $addSubscriptionService)
    {
        $this->userSubscriptionService = $userSubscriptionService;
        $this->addSubscriptionService = $addSubscriptionService;
        $this->em = $em;
    }

    /**
     * @Route("/subscription/{userId}", name="subscription")
     * @IsGranted("ROLE_USER")
     */
    public function getSubscriptions($userId): Response
    {
        $subscriptions = $this->userSubscriptionService->getUserSubscriptions($userId);
        $authors = $this->em->getRepository(Author::class)->findAll();

        return $this->render('subscription/subscription.html.twig',
            ['subscriptions' => $subscriptions,
                'authors' => $authors,]);
    }

    /**
     * @Route("/postSubscription", name="post_subscription")
     * @IsGranted("ROLE_USER")
     */
    public function post(Request $request): Response
    {
        $author = new Author();
        $form = $this->createForm(SubscriptionFormType::class, $author);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            $ifAuthorExist = $this->em->getRepository(Author::class)->findOneBy(['name' => $author->getName()]);
            if (null == $ifAuthorExist) {
                $this->em->persist($author);
                $this->em->flush();
            }

            $this->addSubscriptionService->addSubscription($user, $author->getName());

            return $this->redirectToRoute('subscription', ['userId' => $user->getId()]);
        }

        return $this->render('subscription/formsubscription.html.twig', [
            'subscriptionForm' => $form->createView(),
        ]);
    }
}
