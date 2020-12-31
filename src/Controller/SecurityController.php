<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController
{


    /**
     * @Route("/user/login", name="security_login", methods={"GET", "POST"})
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
//        $user = new User();
//        $error = $authenticationUtils->getLastAuthenticationError();
//        $lastUsername = $authenticationUtils->getLastUsername();
//        return $this->render('security/login.html.twig');

        $user = new User();

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginType::class, $user, [
            'action' => $this->generateUrl('security_login'),
        ]);

        return $this->render('security/login.html.twig', [
            'loginForm' => $form->createView(),
            'error' => $error,
            'lastUserName' => $lastUsername,
        ]);
    }


     /**
     * @Route("/user/logout", name="security_logout", methods={"GET"})
     */
    public function logout()
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    /**
     * @Route("/user/register", name="register_user", methods={"GET", "POST"})
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function register(UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user, [
            'action' => $this->generateUrl('register_user'),
        ]);

        $masterRequest = $this->get('request_stack')->getMasterRequest();
        $form->handleRequest($masterRequest);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setRole('ROLE_USER');

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);

            try {
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Cette adresse email est déjà utilisée');
                return $this->redirectToRoute('user_welcome');
            }

            $this->addFlash('success', 'Compte créé. Vous pouvez maintenant vous connecter');
        }

        return $this->render('security/register_form.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
