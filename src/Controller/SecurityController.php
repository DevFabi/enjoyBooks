<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     * @throws Exception
     */
    public function logout(): Exception
    {
        throw new Exception('Will be intercepted before getting here');
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();

        $form= $this->createForm(AccountType::class, $user, ['is_registration' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/profile", name="profile")
     */
    public function profile(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(AccountType::class, $user, ['is_user_edit' => true]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $this->em->persist($user);
            $this->em->flush();
        }

        return $this->render('user/profile.html.twig',[
            'form' => $form->createView()]);
    }

    /**
     * @Route("/user/update-password", name="user_password")
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $passwordUpdate= new PasswordUpdate();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $user = $this->getUser();
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getPassword()))
            {
                throw new Exception('Wrong old password');
            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setPassword($hash);
                $this->em->persist($user);
                $this->em->flush();

                return $this->redirectToRoute('profile');
            }

        }

        return $this->render('user/password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
