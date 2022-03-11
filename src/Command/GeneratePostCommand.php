<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratePostCommand extends Command
{
    protected static $defaultName = 'app:generate-post';
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $posts = $this->fetchData('https://techcrunch.com/wp-json/wp/v2/posts?per_page=10&context=embed');
        $posts = json_decode($posts, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $output->writeln('Failed to fetch data from posts API');
            return Command::FAILURE;
        }

        $post = $posts[array_rand($posts)];
        $text = $this->fetchData('https://baconipsum.com/api/?type=all-meat&paras=2&start-with-lorem=1');
        $text = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $output->writeln('Failed to fetch data from text API');
            return Command::FAILURE;
        }

        $title = $post['title']['rendered'];
        $postEntity = new Post();
        $postEntity->setTitle($title);
        $postEntity->setPerex(strip_tags($post['excerpt']['rendered']));
        $postEntity->setSlug($post['slug']);
        $postEntity->setText(implode(' ', $text));

        $this->entityManager->persist($postEntity);
        $this->entityManager->flush();

        $output->writeln("Post '$title' was successfully created");

        return Command::SUCCESS;
    }

    /**
     * @param string $url
     * @return string
     */
    private function fetchData(string $url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return (string) $result;
    }
}
