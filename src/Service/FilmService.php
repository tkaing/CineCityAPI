<?php

namespace App\Service;

use App\Entity\Film;
use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class FilmService {

    private $serializer;
    private $finderFilm;

    public function __construct(EntityManagerInterface $manager) {
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        //$this->finderFilm = $manager->getRepository(Film::class);
    }

    public function mapObject(Film $object) {
        $data = $this->serializer->normalize($object);
        $releaseDate = $object->getReleaseDate();
        if ($releaseDate instanceof \DateTime) $data['releaseDate'] = $releaseDate->format('Y-m-d H:i');
        return $data;
    }

    public function mapObjects(array $objects) {
        return array_map(function (Film $object) {
            return $this->mapObject($object);
        }, $objects);
    }
}