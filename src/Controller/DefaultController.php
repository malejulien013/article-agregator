<?php

namespace App\Controller;

use App\Entity\Source;
use App\Form\Type\SearchArticleType;
use App\Form\Type\SourceType;
use App\Services\ArticleAgregator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(ArticleAgregator $articleAgregator): Response
    {
        $articles = $articleAgregator->getArticles();

        return $this->render('index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/articles', name: 'app_articles')]
    public function getArticles(ArticleAgregator $articleAgregator): Response
    {
        $articles = $articleAgregator->getArticles();

        return $this->render('article/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/sources', name: 'app_sources')]
    public function getSources(EntityManagerInterface $entityManager): Response
    {
        $sources = $entityManager->getRepository(Source::class)->findAll();

        return $this->render('source/index.html.twig', [
            'sources' => $sources,
        ]);
    }

    #[Route('/source/add', name: 'app_source_add')]
    public function addSource(
        Request $request,
        EntityManagerInterface $entityManager,
        ArticleAgregator $articleAgregator
    ): Response
    {
        $source = new Source();

        $form = $this->createForm(SourceType::class, $source);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newSource = $form->getData();
            $entityManager->persist($newSource);
            $entityManager->flush();

            switch($newSource->getType()->value) {
                case 'database':
                    $articleAgregator->appendDatabase($newSource);
                    break;

                case 'rss':
                    $articleAgregator->appendRss($newSource);
                    break;

                case 'api':
                    $articleAgregator->appendApi($newSource);
                    break;

                case 'file':
                    $sourceFile = $form->get('attachement')->getData();

                    if ($sourceFile) {
                        try {
                            $articleAgregator->appendFile($newSource, $sourceFile);
                        } catch (FileException $e) {
                            echo sprintf('<p>Erreur lors du traitement du fichier. Erreur : <strong>%s</strong><p>', $e);
                        }
                    }
                    break;

                default:
            }

            return $this->redirectToRoute('app_sources');
        }

        return $this->render('source/add.html.twig', [
            'form' => $form,
        ]);
    }
}
