<?php


namespace App\Controller;

use App\Entity\Article;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController {

    private $manager;

    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;
    }

    /**
     * @Route("/list_art", name="art")
     * @return Response
     */

    public function listArticle(){
        $article = $this->manager->getRepository(Article::class)->findAll();
        return $this->render("list_article.html.twig", ["nom" => "Articles", "art" => $article]);
    }

    /**
     * @Route("/description/{id_art}", name="detail")
     */

    public function detailArticle($id_art){
        $article = $this->manager->getRepository(Article::class)->find($id_art);
        return $this->render("details_art.html.twig", ["nom" => "Details", "details" => $article]);
    }

    /**
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @Route("/create_new_article", methods="POST", name="create_art")
     */

    public function newArticle(Request $request){

        if (empty($request->get("libelle")))
            return $this->redirectToRoute("art");

        $article = new Article();
        $article
            ->setLibelle($request->get('libelle'))
            ->setPu($request->get("pu"))
            ->setQuantite($request->get("qty"))
            ->setStatut($request->get("status"));

        try{
            $this->manager->persist($article);
            $this->manager->flush();
            $this->addFlash(
                "success",
                "L'article à été ajouté avec succès"
            );
        }catch (UniqueConstraintViolationException $e){
            $this->addFlash(
                "warning",
                "Duplication de libellé"
            );
        }

        return $this->redirectToRoute('art');
    }

    /**
     * @param $id_art
     * @Route("/suppression/{id_art}", name="suppression", requirements={"id_art'='\d+"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */

    public function suppression($id_art){
        $article = $this->manager->getRepository(Article::class)->find($id_art);

        $this->manager->remove($article);
        $this->manager->flush();

        return $this->redirectToRoute("art");
    }

    /**
     * @param $id_art
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/update/{id_art}", name="modify")
     */

    public function update($id_art, Request $request){
    $article = $this->manager->getRepository(Article::class)->find($id_art);

    try{
        $article
            ->setLibelle($request->get('libelle'))
            ->setPu($request->get("pu"))
            ->setQuantite($request->get("qty"))
            ->setStatut($request->get("status"));

        $this->manager->persist($article);
        $this->manager->flush();
        $this->addFlash(
            "success",
        "L'article à été modifié avec succès");
    }catch(UniqueConstraintViolationException $e){
        $this->addFlash(
            "warning",
            "L'article ne peut pas être modifié");
    }


    return $this->redirectToRoute("art");
}

}