<?php


namespace App\Controller;

use App\Entity\Personne;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PersonneController extends AbstractController {

    /**
     * @Route("/list_per", name="per")
     * @return Response
     */

    public function listPerson(EntityManagerInterface $manager){
        $person = $manager->getRepository(Personne::class)->findAll();
        return $this->render("list_person.html.twig", ["nom" => "Personnes", "per" => $person]);
    }

    /**
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @Route("/create_new_person", methods="POST", name="create_per")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newPerson(EntityManagerInterface $manager, Request $request){

        if (empty($request->get("nom")))
            return $this->redirectToRoute("per");
        $person = new Personne();

        $person
            ->setNom($request->get("nom"))
            ->setPrenom($request->get("prenom"))
            ->setLogin($request->get("login"))
            ->setMdp($request->get("mdp"))
            ->setEmail($request->get("email"))
            ->setGenre($request->get("genre"));

        try{
            $manager->persist($person);
            $manager->flush();
            $this->addFlash(
                "success",
                "Le membre à été ajouté avec succès"
            );

        }catch (UniqueConstraintViolationException $e){
           $this->addFlash(
                "warning",
                "Duplication de nom"
            );
        }

        return $this->redirectToRoute("per");
    }
}