<?php

namespace App\Controller\Admin;

use App\Entity\Hardware;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class HardwareCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Hardware::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
