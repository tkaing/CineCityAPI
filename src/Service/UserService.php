<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\Film;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UserService {

    private $service;
    private $serializer;
    //private $finderFilm;

    public function __construct(CinemaService $service, EntityManagerInterface $manager) {
        $this->service = $service;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        //$this->finderFilm = $manager->getRepository(Film::class);
    }

    public function mapObject(User $object) {
        $data = $this->serializer->normalize($object);
        $password = $object->getPassword();
        if (is_string($password)) $data['password'] = $this->service->decode($password);
        return $data;
    }

    public function mapObjects(array $objects) {
        return array_map(function (User $object) {
            return $this->mapObject($object);
        }, $objects);
    }
}