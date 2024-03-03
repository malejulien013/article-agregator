<?php

namespace App\Services;

use App\Entity\Article;
use App\Entity\Source;
use App\Repository\ArticleRepository;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ArticleAgregator
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private EntityManagerInterface $entityManager,
        private HttpClientInterface $client,
    ) {

    }

    public function getArticles(): array
    {
        return $this->articleRepository->findAll();
    }

    /**
     * @throws Exception
     */
    public function appendDatabase(Source $source): void
    {
        $dsnParser = new DsnParser();
        $connectionParams = $dsnParser->parse($source->getParameters());
        $conn = DriverManager::getConnection($connectionParams);

        $sql = 'SELECT * FROM articles';
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();
        $dbArticles = $result->fetchAllAssociative();
        $conn->close();

        foreach($dbArticles as $article) {
            $newArticle = new Article();
            $newArticle->setName($article["title"]);
            $newArticle->setSource($source);
            $newArticle->setContent($article["description"]);
            $newArticle->setAuthor($article["author"]);
            $newArticle->setPublicationDate(new \DateTime($article["pubDate"]));

            $this->entityManager->persist($newArticle);
        }
        $this->entityManager->flush();
    }

    public function appendRss(Source $source): void
    {
        $rssArticles = simplexml_load_file($source->getParameters());

        foreach($rssArticles->channel->item as $article) {
            $newArticle = new Article();
            $newArticle->setName($article->title);
            $newArticle->setSource($source);
            $newArticle->setContent($article->description);
            $newArticle->setAuthor($article->author);
            $newArticle->setPublicationDate(new \DateTime($article->pubDate));

            $this->entityManager->persist($newArticle);
        }
        $this->entityManager->flush();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function appendApi(Source $source): void
    {
        $params = explode(',', $source->getParameters());
        $headers = ['Content-Type' => 'application/json'];
        if($params[1] !== null) {
            $headers[] = ['Authorization' => 'Bearer ' . $params[1]];
        }
        $response = $this->client->request('GET', $params[0], [
            'headers' => $headers
        ]);

        $apiArticles = json_decode($response->getContent());

        foreach($apiArticles as $article) {
            $newArticle = new Article();
            $newArticle->setName($article->title);
            $newArticle->setSource($source);
            $newArticle->setContent($article->description);
            $newArticle->setAuthor($article->author);
            $newArticle->setPublicationDate(new \DateTime($article->pubDate));

            $this->entityManager->persist($newArticle);
        }
        $this->entityManager->flush();
    }

    public function appendFile(Source $source, mixed $file): void
    {
        if(($fileContent = fopen($file->getPathname(), "r")) !== false) {
            while (($line = fgetcsv($fileContent)) !== false) {
                $article = explode(";", $line[0]);
                $newArticle = new Article();
                $newArticle->setName($article[0]);
                $newArticle->setSource($source);
                $newArticle->setContent($article[1]);
                $newArticle->setAuthor($article[2]);
                $newArticle->setPublicationDate(new \DateTime($article[3]));

                $this->entityManager->persist($newArticle);
            }
            fclose($fileContent);
            $this->entityManager->flush();
        }
    }
}