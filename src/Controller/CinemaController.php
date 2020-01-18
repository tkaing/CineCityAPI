<?php

namespace App\Controller;

use App\Entity\Film;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CinemaController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to CineCity API !',
            'path' => 'src/Controller/CinemaController.php',
        ]);
    }

    /**
     * @Route("/truncate/film", name="truncate-film")
     */
    public function truncateFilm()
    {
        $classMetaDataFilm = $this->manager->getClassMetadata(Film::class);
        $connection = $this->manager->getConnection();

        try {
            $dbPlatform = $connection->getDatabasePlatform();
            $connection->beginTransaction();
            $connection->query('SET FOREIGN_KEY_CHECKS=0');

            $q = $dbPlatform->getTruncateTableSql($classMetaDataFilm->getTableName());

            $connection->executeUpdate($q);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }

        return $this->json(['success' => true]);
    }
}
