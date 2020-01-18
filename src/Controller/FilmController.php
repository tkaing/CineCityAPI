<?php

namespace App\Controller;

use App\Entity\Film;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    private $validator;
    private $serializer;

    public function __construct(EntityManagerInterface $manager,
                                ValidatorInterface $validator,
                                FilmRepository $finder) {
        $this->finder = $finder;
        $this->manager = $manager;
        $this->validator = $validator;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

    /**
     * @Route("", name="all", methods={"GET"})
     */
    public function all() {

        $objects = $this->finder->findAll();

        if (empty($objects))
            return $this->json(['error' => 'objects not found'], JsonResponse::HTTP_NOT_FOUND);

        return $this->json(array_map(function (Film $object) {
            return $this->serializer->normalize($object);
        }, $objects));
    }

    /**
     * @Route("", name="save", methods={"POST"})
     */
    public function save(Request $request) {

        $object = $this->serializer->deserialize(
            $request->getContent(), Film::class, JsonEncoder::FORMAT
        );

        $errors = $this->validator->validate($object);
        if ($errors->count() > 0) {
            return $this->json(array_map(function (ConstraintViolation $error) {
                return [$error->getPropertyPath() => $error->getMessage()];
            }, iterator_to_array($errors)), JsonResponse::HTTP_PARTIAL_CONTENT);
        }

        $this->manager->persist($object);
        $this->manager->flush();

        return $this->json($this->serializer->normalize($object));
    }

    /**
     * @Route("/{id}", name="one", methods={"GET"})
     */
    public function one(int $id) {

        $object = $this->finder->find($id);

        if (!$object instanceof Film)
            return $this->json(['error' => 'object not found'], JsonResponse::HTTP_NOT_FOUND);

        return $this->json($this->serializer->normalize($object));
    }

    /**
     * @Route("/{id}", name="update", methods={"PATCH"})
     */
    public function update(Request $request, int $id) {

        $object = $this->finder->find($id);

        if (!$object instanceof Film)
            return $this->json(['error' => 'object not found'], JsonResponse::HTTP_NOT_FOUND);

        $object = $this->serializer->deserialize(
            $request->getContent(), Film::class, JsonEncoder::FORMAT,
            [AbstractNormalizer::OBJECT_TO_POPULATE => $object]
        );

        $errors = $this->validator->validate($object);
        if ($errors->count() > 0) {
            return $this->json(array_map(function (ConstraintViolation $error) {
                return [$error->getPropertyPath() => $error->getMessage()];
            }, iterator_to_array($errors)), JsonResponse::HTTP_PARTIAL_CONTENT);
        }

        $this->manager->flush();

        return $this->json($this->serializer->normalize($object));
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
}
