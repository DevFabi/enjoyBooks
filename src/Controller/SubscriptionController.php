<?php


namespace App\Controller;


use App\Form\SubscriptionFormType;
use App\Service\Authors\GetListOfAuthors;
use App\Service\Subscriptions\AddSubscriptionService;
use App\Service\Subscriptions\GetUserSubscriptionService;
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
     * @var GetListOfAuthors
     */
    private $listOfAuthors;
    /**
     * @var AddSubscriptionService
     */
    private $addSubscriptionService;

    /**
     * SubscriptionController constructor.
     * @param GetUserSubscriptionService $userSubscriptionService
     * @param GetListOfAuthors $listOfAuthors
     * @param AddSubscriptionService $addSubscriptionService
     */
    public function __construct(GetUserSubscriptionService $userSubscriptionService, GetListOfAuthors $listOfAuthors, AddSubscriptionService $addSubscriptionService)
    {
        $this->userSubscriptionService = $userSubscriptionService;
        $this->listOfAuthors = $listOfAuthors;
        $this->addSubscriptionService = $addSubscriptionService;
    }

    /**
     * @Route("/subscription/{userId}", name="subscription")
     * @param $userId
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    public function getSubscriptions($userId): Response
    {
        $subscriptions = $this->userSubscriptionService->getUserSubscriptions($userId);
        $authors = $this->listOfAuthors->getAuthors();

        return $this->render('subscription/subscription.html.twig',
                                   ["subscriptions" => $subscriptions,
                                    "authors" => $authors]);
    }

    /**
     * @Route("/postSubscription", name="post_subscription")
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    public function post(Request $request): Response
    {
        $authors = $this->listOfAuthors->getAuthors();

        $nameAuthors = [];
        foreach ($authors as $author){
            $nameAuthors[$author->getName()]= $author->getId();
        }

        $form = $this->createForm(SubscriptionFormType::class,[$nameAuthors]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $this->getUser();
            $this->addSubscriptionService->addSubscription($user, $data);

            return $this->redirectToRoute('subscription', ['userId' => $user->getId()]);
        }

        return $this->render('subscription/formsubscription.html.twig', [
            'subscriptionForm' => $form->createView()
        ]);
    }

}