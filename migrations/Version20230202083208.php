<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Manager;
use App\Entity\SettingManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230202083208 extends AbstractMigration implements ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE setting_manager ADD full_name VARCHAR(255) DEFAULT NULL');
    }

    public function postUp(Schema $schema): void
    {
        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        $repositoryManager = $entityManager->getRepository(Manager::class);
        $repositorySettingManager = $entityManager->getRepository(SettingManager::class);

        $managers = $repositoryManager->findAll();

        foreach ($managers as $manager) {
            $managerName = $repositoryManager->findAllByNumber($manager->getId());
            $managerObj = $repositoryManager->findOneBy(['id' => $manager->getId()]);
            if ($managerName['full_name'] != null && !empty($managerName)) {

                $settingManagers = $repositorySettingManager->findOneBy(['manager' => $manager->getId()]);
                if (empty($settingManagers)) {
                    $settingManagers = new SettingManager();
                }
                $settingManagers->setManager($managerObj);
                $settingManagers->setFullName($managerName['full_name']);
                $entityManager->persist($settingManagers);

            }
        }
        $entityManager->flush();

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE setting_manager DROP full_name');
    }
}