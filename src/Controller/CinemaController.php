<?php

namespace App\Controller;

use App\Entity\Film;
use App\Repository\FilmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CinemaController extends AbstractController
{
    private $manager;
    private $filmRepository;

    public function __construct(EntityManagerInterface $manager, FilmRepository $filmRepository) {
        $this->manager = $manager;
        $this->filmRepository = $filmRepository;
    }

    /**
     * @Route("/cinema", name="cinema")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CinemaController.php',
        ]);
    }

    /**
     * @Route("/film/{id}", name="one-film", methods={"GET"})
     * @param int $id
     * @return JsonResponse
     */
    public function getOne(int $id) {

        $film = $this->filmRepository->find($id);

        return $this->json($film);
    }

    /**
     * @Route("/film", name="all-film", methods={"GET"})
     */
    public function getAll() {

        $films = $this->filmRepository->findAll();

        return $this->json($films);
    }

    /**
     * @Route("/film", name="save-film", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function save(Request $request, ValidatorInterface $validator) {

        $data = json_decode($request->getContent(), true);

        $film = new Film();
        $film->setTitle($data['title'] ?? "");

        $errors = $validator->validate($film);
        if ($errors->count() > 0) {
            return $this->json($errors);
        }

        dump((array) $film); die;

        $this->manager->persist($film);
        //$this->manager->flush();

        return $this->json($film);
    }
}
