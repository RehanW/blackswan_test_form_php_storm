<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact")
     */
    public function show(Environment $twig, Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $contact = new Contact();

        $form = $this->createForm(ContactFormType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($contact);
            $entityManager->flush();

            dump($contact);

            $to = $contact->getEmail();

            $email = (new Email())
                ->from('blackswantest.rehan@gmail.com')
                ->to('blackswantest.rehan@gmail.com')
                ->subject('You have been added to the contact list')
                ->html('<p>Thank you for signing up to the daily news report</p>');
            $mailer->send($email);

            $email2 = (new Email())
                ->from('blackswantest.rehan@gmail.com')
                ->to($to)
                ->subject('You have been added to the contact list')
                ->html('<p>Thank you for signing up to the daily news report</p>');
            $mailer->send($email2);

            return new Response('Contact id = ' . $contact->getId() . ' has been created for Mr/Ms ' . $contact->getFirstName());
        }

        return new Response($twig->render('contact/show.html.twig', [
            'contact_form' => $form->createView()
        ]));
    }
}