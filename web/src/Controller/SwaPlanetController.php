<?php

namespace App\Controller;

use App\Entity\Planet;
use App\Service\SwapiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SwaPlanetController extends AbstractController
{

    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    protected $fields = array('id', 'name', 'rotation_period', 'orbital_period', 'diameter');

    /**
     * @Route("/planets/{id}", methods={"GET"})
     */
    function search(int $id, SwapiService $swapiService): JsonResponse
    {
        return new JsonResponse(
            $swapiService->getPlanetById($id)
        );
    }

    /**
     * @Route("/planet", methods={"POST"})
     */
    public function save(Request $request,  EntityManagerInterface $entityManager)
    {

        $parameters = json_decode($request->getContent(), true);

        foreach (array_keys($parameters) as $parameter) {
            if (!in_array($parameter, $this->fields)) {
                return [
                    "error" => 500,
                    "message" => "Hay más datos de los necesarios."
                ];
            }
        }

        $planet = new Planet();

        $planet->setId($parameters['id']);
        $planet->setName($parameters['name']);

        if (!empty($parameters['diameter'])) {
            $planet->setDiameter($parameters['diameter']);
        }

        if (!empty($parameters['rotation_period'])) {
            $planet->setOrbitalPeriod($parameters['rotation_period']);
        }

        if (!empty($parameters['orbital_period'])) {
            $planet->setRotationPeriod($parameters['orbital_period']);
        }

        $violations = $this->validator->validate($planet);

        if (count($violations) > 0) {
            $messages = [];
            foreach ($violations as $violation) {
                $messages[$violation->getPropertyPath()][] = $violation->getMessage();
            }
            return new JsonResponse($messages);
        }

        $entityManager->persist($planet);
        $entityManager->flush();

        return new JsonResponse(
            array(
                "ok" => 200,
                "message" => "Planeta registrado con exito"
            )
        );
    }
}