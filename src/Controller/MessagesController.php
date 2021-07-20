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
        ->add('name',TextType::class, ['constraints'=>[
            new NotBlank,
            new Length(['min'=>'3'])
        ]])
        ->add('email',EmailType::class, ['constraints'=>[
            new NotBlank,
            new Email
        ]])
        ->add('message',TextareaType::class, ['constraints'=>[
            new NotBlank,
            new Length(['min'=>'5'])
        ]])
        ->getForm()
        ;

        $form->handleRequest($request);
if($form->isSubmitted() && $form->isValid()){
    $formData = $form->getData();
    dump(sprintf( "Sending mail from %s <%s>",$formData['name'],$formData['email']));
    $this->addFlash('success','Message sent ! Get back soon');
    return $this->redirectToRoute('app_home');
}

        return $this->render('messages/new.html.twig',['form'=>$form->createView()]);
    }
}
