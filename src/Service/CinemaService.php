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
     * Extract attribute and message.
     * @param ConstraintViolationListInterface $errors
     * @return array
     */
    public function mapErrors(ConstraintViolationListInterface $errors) {
        return array_map(function (ConstraintViolation $error) {
            return [ $error->getPropertyPath() => $error->getMessage() ];
        }, iterator_to_array($errors));
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
     * Convert string to DateTime Object.
     * @param array $array
     * @param array $columns
     * @param string $class
     * @return array
     */
    public function decode(array $array, array $columns, string $class) {
        array_walk($array, function (&$item, $k) use ($columns, $class) {
            if (in_array($k, $columns) && $class == \DateTime::class) {
                $item = new \DateTime($item);
            }
        });
        return $array;
    }
}
