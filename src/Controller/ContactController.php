<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactDTO();

        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $email = (new TemplatedEmail())
                    ->from($data->email)
                    ->to($data->service)
                    ->subject('Demande de contact')
                    ->htmlTemplate('emails/contact.html.twig')
                    ->context(['data' => $data]);
                $mailer->send($email);
                $this->addFlash('success', 'Your email has been successfully sent');
                return $this->redirectToRoute('contact');
            } catch(Exception $e) {
                $this->addFlash('danger', "Your email couldn't be sent");
            }
            
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
