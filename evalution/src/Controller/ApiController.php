<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Film;

class ApiController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

     /**
     * @Route("/api", name="api")
     */
    public function indexApi(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }



      /**
     * @Route("/api/create", name="create")
     */
    public function create(Request $request)
    {
            $film = new Film();
            
            $form = $this->createFormBuilder($film)
            ->add('Nom', TextType::class)
            ->add('Synopsis', TextType::class)
            ->add('Type', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Valider'])
            ->getForm();



            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $film->setDateCreation(new \DateTime());
                $film = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $em->persist($film);
                $em->flush();
              
                return new Response("Created", 201);
            }if ($form->isSubmitted() && !$form->isValid()){
                return new Response("problem", 204);
            }
            return $this->render('api/create.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    

  /**
     * @Route("/api/getall", name="getall")
     */
    public function getAll(): Response
    {
        $films = $this->getDoctrine()->getRepository(Film::class);
        $films = $films->findAll();
     /*   return $this->render(
            'api/getall.html.twig',
             array('films' => $films),
        );
        */
        $data = [];

        foreach ($films as $film){
            $data[] = [
                'id' => $film->getId(),
                'nom' => $film->getNom(),
                'type' => $film->getType(),
                'date' => $film->getDateCreation()
            ];
        }

         return new JsonResponse($data, Response::HTTP_OK);
    }

 /**
     * @Route("/api/getone/{id}", name="getone")
     */
  
        public function getone($id) : JsonResponse {
            $film = $this->getDoctrine()->getRepository(Film::class);
            $film = $film->find($id);
            if (!$film) {
                return new Response("Page Does not exist", 404);
            }
        /*    return $this->render(
                'api/getone.html.twig',
                array('film' => $film)
            ); */
            $data = [
                'id' => $film->getId(),
                'nom' => $film->getNom(),
                'type' => $film->getType(),
                'date' => $film->getDateCreation()
              
            ];

            return new JsonResponse($data, Response::HTTP_OK);

        }


}
