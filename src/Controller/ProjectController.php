<?php

namespace App\Controller;

use App\Entity\SettingProject;
use App\Model\MockData;
use App\Model\Response\BadResponse;
use App\Model\Response\SettingProjectResponse;
use App\Model\SettingProjectDTO;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\ClientEvo;
use OpenApi\Annotations as OA;
use App\Model\Response\ProjectResponse;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @Route("/api/doc")
 */
class ProjectController extends AbstractController
{
    private $doctrine;

    private $clientEvo;

    private $logger;

    private $mockData;

    public function __construct(
        ManagerRegistry $doctrine,
        ClientEvo $clientEvo,
        LoggerInterface  $evoApiLogger,
        MockData $mockData
    ) {
        $this->doctrine = $doctrine;
        $this->clientEvo = $clientEvo;
        $this->logger = $evoApiLogger;
        $this->clientEvo->setLogger($this->logger);
        $this->mockData = $mockData;
    }

    /**
     * Получение всех проектов
     *
     * @Route("/project", name="project-all", methods={"GET"})
     * @OA\Tag(name="Project")
     *
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *        @OA\JsonContent(ref=@Model(type=ProjectResponse::class))
     * )
     * @return JsonResponse
     */
    public function getProjectsAll(): JsonResponse
    {
        $request = Request::createFromGlobals();

        $maskBearer = 'Bearer ';
        $bearer = $request->headers->get('authorization');
        $bearerToken = str_replace($maskBearer, "", $bearer);

        $parameters = [
            'token' => $bearerToken
        ];

        $evoAnswer = $this->clientEvo->getProject($parameters);
        if (empty($evoAnswer['data'])) {
            return new JsonResponse(new BadResponse(false, $evoAnswer['status']), JsonResponse::HTTP_BAD_REQUEST);
        }

        //test
        $evoAnswer['data'] = $this->mockData->getProject();
        //test

        $projectResponse = new ProjectResponse(true);
        $projectResponse->projects = $evoAnswer['data'];

        return new JsonResponse($projectResponse, JsonResponse::HTTP_OK);
    }

    /**
     * Получение настройки проекта
     *
     * @Route("/project/setting/{id}", name="getSettingProject", methods={"GET"})
     * @OA\Tag(name="Project")
     *
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *       @OA\JsonContent(ref=@Model(type=SettingProjectResponse::class))
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Project id",
     *     @OA\Schema(type="string")
     * )
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getSettingProject(int $id): JsonResponse
    {
        $entityManager = $this->doctrine->getManager();

        $settingProjectManager = $entityManager->getRepository(SettingProject::class);
        $settingProject = $settingProjectManager->findOneBy([
            'projectId' => $id,
        ]);
        if (empty($settingProject)) {
            return new JsonResponse(new BadResponse(false, 'Setting project not exist!'), JsonResponse::HTTP_OK);
        }
        $settingProjectDto = new SettingProjectDTO();
        $settingProjectDto->id = $settingProject->getId();
        $settingProjectDto->lidId = $settingProject->getLidId();
        $settingProjectDto->projectId = $settingProject->getProjectId();

        return new JsonResponse(new SettingProjectResponse(true, $settingProjectDto), JsonResponse::HTTP_OK);
    }

    /**
     * Редактирование настроек проекта
     *
     * @Route("/project/setting/{id}", name="editSettingProject", methods={"POST"})
     * @OA\Tag(name="Project")
     *
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(
     *             property="lidId",
     *             type="string",
     *             example="..."
     *         )
     *     )
     * )
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *       @OA\JsonContent(ref=@Model(type=SettingProjectResponse::class))
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Project id",
     *     @OA\Schema(type="string")
     * )
     *
     * @param int $id
     * @return JsonResponse
     */
    public function editSettingProject(int $id): JsonResponse
    {
        $entityManager = $this->doctrine->getManager();
        $settingProjectManager = $this->doctrine->getRepository(SettingProject::class);
        $request = Request::createFromGlobals();

        $request = json_decode($request->getContent(), true);

        $lidId = $request['lidId'];
        try {
            $settingProject = $settingProjectManager->findOneBy([
                'projectId' => $id,
            ]);

            if (empty($settingProject)) {
                $settingProject = new SettingProject();
            }

            $settingProject->setProjectId($id);
            $settingProject->setLidId($lidId);

            $entityManager->persist($settingProject);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(new BadResponse(false, $e->getMessage()), JsonResponse::HTTP_BAD_REQUEST);
        }

        $settingProjectDto = new SettingProjectDTO();
        $settingProjectDto->id = $settingProject->getId();
        $settingProjectDto->lidId = $settingProject->getLidId();
        $settingProjectDto->projectId = $settingProject->getProjectId();

        return new JsonResponse(new SettingProjectResponse(true, $settingProjectDto), JsonResponse::HTTP_OK);
    }
}
