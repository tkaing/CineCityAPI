<?php

namespace App\Service;

use App\Entity\Film;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TicketService {

    private $serializer;
    private $finderFilm;
    private $finderUser;
    private $serviceFilm;
    private $serviceUser;

    public function __construct(EntityManagerInterface $manager,
                                FilmService $serviceFilm,
                                UserService $serviceUser) {
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->finderFilm = $manager->getRepository(Film::class);
        $this->finderUser = $manager->getRepository(User::class);
        $this->serviceFilm = $serviceFilm;
        $this->serviceUser = $serviceUser;
    }

    public function mapObject(Ticket $object) {
        $data = $this->serializer->normalize($object);
        $date = $object->getDate();
        if ($date instanceof \DateTime) $data['date'] = $date->format('Y-m-d H:i');
        $film = ($object->getFilm() === null) ? null : $this->finderFilm->find($object->getFilm());
        if ($film instanceof Film) $data['film'] = $this->serviceFilm->mapObject($film);
        $user = ($object->getUser() === null) ? null : $this->finderUser->find($object->getUser());
        if ($user instanceof User) $data['user'] = $this->serviceUser->mapObject($user);
        return $data;
    }

    public function mapObjects(array $objects) {
        return array_map(function (Ticket $object) {
            return $this->mapObject($object);
        }, $objects);
    }
}