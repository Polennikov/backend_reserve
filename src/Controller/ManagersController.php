<?php

namespace App\Controller;

use App\Entity\SettingManager;
use App\Model\EvoDTO\ManagerDTO;
use App\Model\Response\BadResponse;
use App\Model\Response\ManagerResponse;
use App\Model\Response\SettingManagerResponse;
use App\Model\Response\SuccessResponse;
use App\Model\SettingManagerDTO;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Manager;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Model\Request\Evo\SettingManagerRequest;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/doc")
 */
class ManagersController extends AbstractController
{
    private $doctrine;

    private $validator;

    public function __construct(
        ManagerRegistry $doctrine,
        ValidatorInterface  $validator
    ) {
        $this->doctrine = $doctrine;
        $this->validator = $validator;
    }

    /**
     * Получение всех неджеров из БД
     *
     * @Route("/manager", name="getManagers", methods={"GET"})
     * @OA\Tag(name="Manager")
     *
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(ref=@Model(type=ManagerResponse::class))
     * )
     * @return JsonResponse
     */
    public function getManagers(): JsonResponse
    {
        $managerRepository = $this->doctrine->getRepository(Manager::class);
        $managerSettingRepository = $this->doctrine->getRepository(SettingManager::class);

        $arManagers = [];
        $managers = $managerRepository->findAll();
        foreach ($managers as $manager) {
            $settingManager = $managerSettingRepository->findOneBy([
                'manager' => $manager->getId(),
            ]);
            $arManagers[$manager->getId()] = [
                'login' => $manager->getLogin(),
                'managerName' =>  isset($settingManager) ? $settingManager->getFullName() : '',
            ];
        }
        $dataResponse = [
            'success' => true,
            'managers' => $arManagers,
        ];

        return new JsonResponse($dataResponse, JsonResponse::HTTP_OK);
    }

    /**
     * Получение настроек менеджера
     *
     * @Route("/manager/setting/{id}", name="getSettingManager", methods={"GET"})
     * @OA\Tag(name="Manager")
     *
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(ref=@Model(type=SettingManagerResponse::class))
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Project id",
     *     @OA\Schema(type="string")
     * )
     * @param $id
     * @return JsonResponse
     */
    public function getSettingManager(int $id): JsonResponse
    {
        $settingRepository = $this->doctrine->getRepository(SettingManager::class);

        $settingManager = $settingRepository->findOneBy([
            'manager' => $id,
        ]);

        if (empty($settingManager)) {
            return new JsonResponse(new SuccessResponse(false), JsonResponse::HTTP_OK);
        }

        $settingManagerDTO = new SettingManagerDTO();
        $settingManagerDTO->countMonth = $settingManager->getCountMonth();
        $settingManagerDTO->projectsSidebar = $settingManager->getProjectsSidebar();
        $settingManagerDTO->name = $settingManager->getFullName();

        return new JsonResponse(new SettingManagerResponse(true, $settingManagerDTO),JsonResponse::HTTP_OK);
    }

    /**
     * Редактирование настроек менеджера
     *
     * @Route("/manager/setting/{id}", name="editManagerSetting", methods={"POST"})
     * @OA\Tag(name="Manager")
     *
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(ref=@Model(type=SettingManagerResponse::class))
     * )
     * @OA\RequestBody(
     *     required=true,
     *     description="Данные пользователя",
     *     @OA\JsonContent(ref=@Model(type=SettingManagerRequest::class))
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Manager id",
     *     @OA\Schema(type="string")
     * )
     * @param $id
     * @return JsonResponse
     */
    public function editManagerSetting(int $id): JsonResponse
    {
        $serializer = SerializerBuilder::create()->build();
        $entityManager = $this->doctrine->getManager();
        $settingRepository = $this->doctrine->getRepository(SettingManager::class);
        $request = Request::createFromGlobals();

        $maskBearer = 'Bearer ';
        $bearer = $request->headers->get('authorization');
        $bearerToken = str_replace($maskBearer, "", $bearer);

        $settingManagerRequest = $serializer->deserialize($request->getContent(), SettingManagerRequest::class, 'json');
        $errors = $this->validator->validate($settingManagerRequest);

        if (count($errors) > 0) {
            return new JsonResponse(new BadResponse(false, 'Valid param error'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $settingManager = $settingRepository->findOneBy([
            'manager' => $id,
        ]);
        //print_r($settingManagerRequest);
        try {
            if (empty($settingManager)) {
                $settingManager = new SettingManager();
                $managerRepository = $this->doctrine->getRepository(Manager::class);
                $manager = $managerRepository->findOneBy([
                    'id' => $id,
                ]);
                $settingManager->setManager($manager);
            }
            if (isset($settingManagerRequest->countMonth)) {
                $settingManager->setCountMonth($settingManagerRequest->countMonth);
            }
            if (isset($settingManagerRequest->projectsSidebar)) {
                //print_r($settingManagerRequest->projectsSidebar);
                $settingManager->setProjectsSidebar($settingManagerRequest->projectsSidebar);
            }
            if (isset($settingManagerRequest->name)) {
                $settingManager->setFullName($settingManagerRequest->name);
            }

            $entityManager->persist($settingManager);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(new BadResponse(false, $e->getMessage()), JsonResponse::HTTP_BAD_REQUEST);
        }

        $settingManagerDTO = new SettingManagerDTO();
        $settingManagerDTO->countMonth = $settingManager->getCountMonth();
        $settingManagerDTO->projectsSidebar = $settingManager->getProjectsSidebar();
        $settingManagerDTO->name = $settingManager->getFullName();

        return new JsonResponse(new SettingManagerResponse(true, $settingManagerDTO), JsonResponse::HTTP_OK);
    }
}
