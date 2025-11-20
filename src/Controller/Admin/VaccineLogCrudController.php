<?php

namespace App\Controller\Admin;

use App\Entity\VaccineLog;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class VaccineLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return VaccineLog::class;
    }
}
