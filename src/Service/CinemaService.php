<?php

namespace App\Service;

use App\Entity\Film;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CinemaService {

    private $serializer;

    public function __construct() {
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

    /**
     * Convert object to array.
     * @param array $objects
     * @return array
     */
    public function mapObjects(array $objects) {
        return array_map(function ($object) {
            return $this->serializer->normalize($object);
        }, $objects);
    }

    /**
     * Extract attribute and message.
     * @param ConstraintViolationListInterface $errors
     * @return array
     */
    public function mapErrors(ConstraintViolationListInterface $errors) {
        return array_map(function (ConstraintViolation $error) {
            return [ $error->getPropertyPath() => $error->getMessage() ];
        }, iterator_to_array($errors));
    }
}
