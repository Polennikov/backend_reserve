<?php

namespace App\Controller\Admin;

use App\Entity\Manager;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Manager::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('login'),
            TextField::new('token'),
            TextField::new('roleUser'),
            TextField::new('competence'),

        ];
    }

}
return [


];
