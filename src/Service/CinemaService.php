<?php

namespace App\Service;

use App\Entity\Film;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CinemaService {

    private $params;
    private $serializer;

    public function __construct(ParameterBagInterface $params) {
        $this->params = $params;
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
     * Encode id.
     *
     * @param int $decoded
     * @return string
     */
    public function encode($decoded) {
        return base64_encode($decoded . getenv('APP_SECRET'));
    }

    /**
     * Decode id.
     *
     * @param string $encoded
     * @return string
     */
    public function decode($encoded) {
        return preg_replace(sprintf('/%s/', getenv('APP_SECRET')), '', base64_decode($encoded));
    }
}
