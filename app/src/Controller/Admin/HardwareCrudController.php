<?php

namespace App\Controller\Admin;

use App\Entity\Hardware;
use App\Repository\RoleRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{TextEditorField, TextField, AssociationField};

class HardwareCrudController extends AbstractCrudController
{
    private RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public static function getEntityFqcn(): string
    {
        return Hardware::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextEditorField::new('description'),
            TextField::new('ipV4'),
            TextField::new('vpnUserName'),
            AssociationField::new('roles')
                ->setFormTypeOption('by_reference', false)
        ];
    }
}
