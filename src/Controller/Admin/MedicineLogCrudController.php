<?php

namespace App\Controller\Admin;

use App\Entity\MedicineLog;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MedicineLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MedicineLog::class;
    }
}
