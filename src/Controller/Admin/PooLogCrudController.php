<?php

namespace App\Controller\Admin;

use App\Entity\PooLog;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PooLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PooLog::class;
    }
}
