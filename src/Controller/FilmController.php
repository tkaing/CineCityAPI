<?php

namespace App\Controller;

use App\Entity\Film;
use App\Repository\FilmRepository;
use App\Service\CinemaService;
use App\Service\FilmService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/film", name="film-")
 */
class FilmController extends AbstractController
{
    private $finder;
    private $manager;
    private $service;
    private $validator;
    private $serializer;
    private $serviceFilm;

    public function __construct(EntityManagerInterface $manager,
                                ValidatorInterface $validator,
                                CinemaService $service,
                                FilmRepository $finder,
                                FilmService $serviceFilm) {
        $this->finder = $finder;
        $this->manager = $manager;
        $this->service = $service;
        $this->validator = $validator;
        $this->serializer = new Serializer([new DateTimeNormalizer(), new ObjectNormalizer(null, null, null, new ReflectionExtractor())], [new JsonEncoder()]);
        $this->serviceFilm = $serviceFilm;
    }

    /**
     * @Route("/by", name="by", methods={"GET"})
     */
    public function by(Request $request) {

        $array = $this->serializer->decode($request->getContent(), JsonEncoder::FORMAT);
        //$array['releaseDate'] = new \DateTime($array['releaseDate']);

        $objects = $this->finder->findBy($array);

        return $this->json($this->serviceFilm->mapObjects($objects));
    }

    /**
     * @Route("/all", name="all", methods={"GET"})
     */
    public function all() {

        $objects = $this->finder->findAll();

        return $this->json($this->serviceFilm->mapObjects($objects));
    }

    /**
     * @Route("/{id}", name="one", methods={"GET"})
     */
    public function one(int $id) {

        $object = $this->finder->find($id);

        if (!$object instanceof Film)
            return $this->json(['error' => 'object not found'], JsonResponse::HTTP_NOT_FOUND);

        return $this->json($this->serviceFilm->mapObject($object));
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(int $id) {

        $object = $this->finder->find($id);

        if (!$object instanceof Film)
            return $this->json(['error' => 'object not found'], JsonResponse::HTTP_NOT_FOUND);

        $this->manager->remove($object);
        $this->manager->flush();

        return $this->json(['id' => $id]);
    }

    /**
     * @Route("/{id}", name="update", methods={"PATCH"})
     */
    public function update(Request $request, int $id) {

        $object = $this->finder->find($id);

        if (!$object instanceof Film)
            return $this->json(['error' => 'object not found'], JsonResponse::HTTP_NOT_FOUND);

        $object = $this->serializer->deserialize($request->getContent(), Film::class, JsonEncoder::FORMAT, [
            AbstractNormalizer::OBJECT_TO_POPULATE => $object
        ]);

        $errors = $this->validator->validate($object);
        if ($errors->count() > 0) {
            return $this->json($this->service->mapErrors($errors), JsonResponse::HTTP_PARTIAL_CONTENT);
        }

        $this->manager->flush();

        return $this->json($this->serviceFilm->mapObject($object));
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(Request $request) {

        $object = $this->serializer->deserialize($request->getContent(), Film::class, JsonEncoder::FORMAT);

        $errors = $this->validator->validate($object);
        if ($errors->count() > 0) {
            return $this->json($this->service->mapErrors($errors), JsonResponse::HTTP_PARTIAL_CONTENT);
        }

        $this->manager->persist($object);
        $this->manager->flush();

        return $this->json($this->serviceFilm->mapObject($object));
    }
}
