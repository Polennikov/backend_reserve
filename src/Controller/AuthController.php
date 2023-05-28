<?php

namespace App\Controller;

use App\Entity\SettingManager;
use App\Model\AuthDTO;
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
    private ManagerRegistry $doctrine;

    private ClientEvo $clientEvo;

    private LoggerInterface  $logger;

    public function __construct(
        ManagerRegistry $doctrine,
        ClientEvo $clientEvo,
        LoggerInterface $authApiLogger
    ) {
        $this->doctrine = $doctrine;
        $this->clientEvo = $clientEvo;
        $this->logger = $authApiLogger;
        $this->clientEvo->setLogger($this->logger);
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
            'username' => $login,
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
            'loginRm' => $manager->getTokenRm(),
            'userId' => $manager->getId(),
            'userName' => $managerName,
        ];

        return new JsonResponse($data, JsonResponse::HTTP_CREATED);
    }

    /**
     * Авторизация пользователя в РМ
     *
     * @Route("/loginRm", name="loginRm", methods={"POST"})
     * @OA\Tag(name="Auth")
     *
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="success",
     *              type="boolean"
     *          ),
     *          @OA\Property(
     *              property="token",
     *              type="string"
     *          )
     * )    )
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(
     *             property="token",
     *             type="string",
     *             example="..."
     *         ),
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
     * @OA\Parameter(
     *     name="token",
     *     in="query",
     *     description="Токен из Evo",
     *     @OA\Schema(type="string")
     * )
     * @param Request $request
     * @return JsonResponse
     * @throws \Redmine\Exception\ClientException
     */
    public function loginRm(Request $request): JsonResponse
    {
        $entityManager = $this->doctrine->getManager();
        $managerRepository = $this->doctrine->getRepository(Manager::class);
        $serializer = SerializerBuilder::create()->build();

        try {
            $tokenEvo = (string)$request->query->get('token');

            $authDTO = $serializer->deserialize($request->getContent(), AuthDTO::class, 'json');

        } catch (\Exception $e) {
            return new JsonResponse(new BadResponse(false, $e->getMessage()), JsonResponse::HTTP_BAD_REQUEST);
        }
        if (empty($authDTO->token)) {
            if (empty($authDTO->login) || empty($authDTO->password)) {
                return new JsonResponse(new BadResponse(false, 'Param is not exist!'), JsonResponse::HTTP_BAD_REQUEST);
            }

            $clientRm = new ClientRm($authDTO->login, $authDTO->password);
            $clientRm->setLogger($this->logger);
            $result = $clientRm->getCurrentUser();

            if (empty($result)) {
                return new JsonResponse(new BadResponse(false, 'getCurrentUser is not exist!'), JsonResponse::HTTP_BAD_REQUEST);
            }

            $manager = $managerRepository->findOneBy([
                'token' => $tokenEvo,
            ]);
            if (empty($manager)) {
                return new JsonResponse(new BadResponse(false, 'Manager is not exist!'), JsonResponse::HTTP_BAD_REQUEST);
            }

            $manager->setTokenRm($result['api_key']);
            $entityManager->persist($manager);
            $entityManager->flush();

            $dataResponse = [
                'success' => true,
                'token' => $manager->getTokenRm(),
            ];

            return new JsonResponse($dataResponse, JsonResponse::HTTP_OK);
        } else {
            if (empty($authDTO->token)) {
                return new JsonResponse(new BadResponse(false, 'Param is not exist!'), JsonResponse::HTTP_BAD_REQUEST);
            }
            $clientRm = new ClientRm($authDTO->token);
            $clientRm->setLogger($this->logger);
            $result = $clientRm->getCurrentUser();

            $dataResponse = [
                'success' => true,
                'token' => $result['api_key'],
            ];

            return new JsonResponse($dataResponse, JsonResponse::HTTP_OK);
        }
    }
}