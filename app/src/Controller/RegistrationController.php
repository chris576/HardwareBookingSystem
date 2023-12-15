<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MailServiceContainer;
use App\Service\RedisContainer;

#[Route('/register', name: 'app_register')]
class RegistrationController extends AbstractController
{
    private MailServiceContainer $mailServiceContainer;
    private RedisContainer $userVerifyRedisContainer;
    private UserRepository $userRepository;

    private RoleRepository $roleRepository;

    public function __construct(RedisContainer $userVerifyRedisContainer, MailServiceContainer $mailServiceContainer, UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userVerifyRedisContainer = $userVerifyRedisContainer;
        $this->mailServiceContainer = $mailServiceContainer;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    #[Route('/confirm', name: '_confirm', methods: 'GET')]
    public function initializeRegister(Request $request)
    {
        $token = $request->query->get("token");
        $email = $request->query->get("email");
        $redis_entry = json_decode($this->userVerifyRedisContainer->get($email), true);
        if ($redis_entry !== null && $redis_entry["token"] == $token) {
            $hashed_password = $redis_entry["hashed_password"];
            if ($hashed_password != null && $hashed_password != "") {
                $user = new User();
                $user->setPassword($hashed_password);
                $user->setEmail($email);
                $user->addUserRole($this->roleRepository->findOneBy(["name" => "ROLE_USER"]));
                $isInternal = explode('@', $user->getEmail())[1] == 'hs-emden-leer.de';
                if ($isInternal) {
                    $user->addUserRole($this->roleRepository->findOneBy(["name" => "ROLE_INTERN"]));
                } else {
                    $user->addUserRole($this->roleRepository->findOneBy(["name" => "ROLE_EXTERN"]));
                }
                $this->userRepository->save($user, true);
                $this->addFlash('success', "Du wurdest erfolgreich verifiziert.");
            } else {
                $this->addFlash('error', "Die Verifizierung ist fehlgeschlagen.");
            }
        } else {
            $this->addFlash('error', "Die Verifizierung ist fehlgeschlagen.");
        }
        return $this->redirectToRoute("app_home");
    }

    #[Route('/', name: '', methods: "GET|POST")]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $token = hash('sha256', random_bytes(100));
            $this->userVerifyRedisContainer->set($user->getEmail(), json_encode(["token" => $token, "hashed_password" => $user->getPassword()]));
            $this->mailServiceContainer->send($user->getEmail(), $this->getParameter('mail.sender.default'), 'Bestätige deine Registrierung!', 'registration/mail.confirmation.html.twig', [
                'url' => $this->generateUrl('app_register_confirm', ['token' => $token, 'email' => $user->getEmail()], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL)
            ]);
            // do anything else you need here, like send an email
            return $this->redirectToRoute('app_home');
        }
        $this->addFlash('success', "Deine Registrierung war erfolgreich und muss jetzt noch von dir bestätigt werden.");
        return $this->render('registration/registration.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
