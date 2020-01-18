<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CinemaController extends AbstractController
{
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
}
