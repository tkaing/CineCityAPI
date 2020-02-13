<?php

namespace App\Service;

use App\Entity\Film;
use App\Entity\Session;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SessionService {

    private $serializer;
    private $finderFilm;
    private $serviceFilm;

    public function __construct(EntityManagerInterface $manager,
                                FilmService $serviceFilm) {
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->finderFilm = $manager->getRepository(Film::class);
        $this->serviceFilm = $serviceFilm;
    }

    public function mapObject(Session $object) {
        $data = $this->serializer->normalize($object);
        $film = ($object->getFilm() === null) ? null : $this->finderFilm->find($object->getFilm());
        if ($film instanceof Film) $data['film'] = $this->serviceFilm->mapObject($film);
        return $data;
    }

    public function mapObjects(array $objects) {
        return array_map(function (Session $object) {
            return $this->mapObject($object);
        }, $objects);
    }
}