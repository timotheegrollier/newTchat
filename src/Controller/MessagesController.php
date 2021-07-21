<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessagesController extends AbstractController
{
    /**
     * @Route("/contact", name="app_contact")
     */
    public function new(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, ['constraints' => [
                new NotBlank,
                new Length(['min' => '3'])
            ]])
            ->add('email', EmailType::class, ['constraints' => [
                new NotBlank,
                new Email
            ]])
            ->add('message', TextareaType::class, ['constraints' => [
                new NotBlank,
                new Length(['min' => '5'])
            ]])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            dump(sprintf("Incoming mail from %s <%s>", $formData['name'], $formData['email']));
            if (strpos($request->headers->get('accept'), 'vnd.turbo-stream.html') == 5) {
                return new Response($this->renderView("messages/success.stream.html.twig", ['name' => $formData['name']]), 200, ['Content-Type' => 'text/vnd.turbo-stream.html']);
            } else
                $this->addFlash('success', 'Message sent ! Get back soon');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        // Si je n'utilise pas renderForm() on vérifie si le formulaire est soumis mais invalide on set le statusCode(422)

        // if ($form->isSubmitted() && !$form->isValid()) {
        //     $response = new Response;
        //     $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        //     return $this->render('messages/new.html.twig', ['form' => $form->createView()], $response);
        //     // return new Response($content,Response::HTTP_UNPROCESSABLE_ENTITY);
        // }
        // Sinon simplement

        return $this->renderForm('messages/new.html.twig', ['form' => $form]);
    }
}