<?php

namespace App\Controller;

use App\Entity\SettingManager;
use App\Model\AuthDTO;
use App\Model\MockData;
use App\Model\Response\BadResponse;
use App\Service\ClientRm;
use JMS\Serializer\SerializerBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\ClientEvo;
use App\Entity\Manager;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/doc")
 */
class AuthController extends AbstractController
{
    private $doctrine;

    private $clientEvo;

    private $logger;

    private $mockData;

    public function __construct(
        ManagerRegistry $doctrine,
        ClientEvo $clientEvo,
        LoggerInterface $authApiLogger,
        MockData $mockData
    ) {
        $this->doctrine = $doctrine;
        $this->clientEvo = $clientEvo;
        $this->logger = $authApiLogger;
        $this->clientEvo->setLogger($this->logger);
        $this->mockData = $mockData;
    }

    /**
     * Авторизация пользователя
     *
     * @Route("/login", name="login", methods={"POST"})
     * @OA\Tag(name="Auth")
     *
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(
     *             property="login",
     *             type="string",
     *             example="..."
     *         ),
     *         @OA\Property(
     *             property="password",
     *             type="string",
     *             example="..."
     *         )
     *     )
     * )
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *       @OA\JsonContent(
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="token",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="login",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="userId",
     *                  type="int"
     *              ),
     *              @OA\Property(
     *                  property="userName",
     *                  type="string"
     *              )
     *          )
     *   )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $serializer = SerializerBuilder::create()->build();
        $entityManager = $this->doctrine->getManager();
        $managerRepository = $this->doctrine->getRepository(Manager::class);
        $managerSettingRepository = $this->doctrine->getRepository(SettingManager::class);
        $authDTO = $serializer->deserialize($request->getContent(), AuthDTO::class, 'json');

        $login = $authDTO->login;
        $password = $authDTO->password;

        if (empty($login) || empty($password)) {
            return $this->json([
                'success' => false,
                'status' => 'empty_fields',
            ]);
        }

        $parameters = [
            'username' => 'polennikov',//$login,
            'password' => $password,
        ];

        $evoAnswer = $this->clientEvo->auth($parameters);
        if (empty($evoAnswer['token'])) {
            return new JsonResponse(new BadResponse(false, 'Error request Evo API'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $manager = $managerRepository->findOneBy([
            'login' => $login,
        ]);

        if (empty($manager)) {
            $manager = new Manager();
            $manager->setToken($evoAnswer['token']);
            $manager->setLogin($login);
            $entityManager->persist($manager);
            $entityManager->flush();
        } else {
            $manager->setToken($evoAnswer['token']);
            $entityManager->persist($manager);
            $entityManager->flush();
        }

        $settingManager = $managerSettingRepository->findOneBy([
            'manager' => $manager->getId(),
        ]);

        $managerName = null;
        if (isset($settingManager)) {
            $managerName = $settingManager->getFullName();
        }

        $data = [
            'success' => true,
            'token' => $evoAnswer['token'],
            'login' => $login,
            'competence' => $manager->getCompetence(),
            'userId' => $manager->getId(),
            'userName' => $managerName,
            'roleUser' => $manager->getRoleUser(),
        ];

        return new JsonResponse($data, JsonResponse::HTTP_CREATED);
    }
}
