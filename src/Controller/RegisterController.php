<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Controller used to register user.
 */
final class RegisterController extends AbstractController
{
    #[Route('/register', name: 'register', defaults: ['_format' => 'html'], methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
        string $_format): Response
    {
        $user = new User();

        $form = $this->createForm(UserRegisterType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                $errorsString = (string) $errors;

                return new Response($errorsString);
            }

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            $user->setRoles([User::ROLE_ADMIN]);

            $entityManager->persist($user);

            $entityManager->flush();

            $this->addFlash('success', 'user.registered');

            return $this->redirectToRoute('admin_post_index');
        }
        return $this->render('user/register.'.$_format.'.twig', [
            'form' => $form,
        ]);
    }
}