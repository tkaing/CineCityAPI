<?php

namespace App\Controller;

use App\Entity\Film;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/date/{timestamp}", name="date", methods={"GET"})
     */
    public function date(int $timestamp) {

        $object = new \DateTime();
        $object->setTimestamp($timestamp);

        return $this->json(['date' => $object->format('Y-m-d H:i:s')]);
    }
}
