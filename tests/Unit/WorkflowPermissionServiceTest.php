<?php

namespace Tests\Unit;

use App\Services\WorkflowPermissionService;
use PHPUnit\Framework\TestCase;

class WorkflowPermissionServiceTest extends TestCase
{
    public function test_validation_roles_follow_the_expected_order(): void
    {
        $service = new WorkflowPermissionService();

        $this->assertSame(1, $service->getNiveauValidationParPoste('RESP_SERVI'));
        $this->assertSame(1, $service->getNiveauValidationParPoste('RESPONSABLE'));
        $this->assertSame(2, $service->getNiveauValidationParPoste('RH'));
        $this->assertSame(3, $service->getNiveauValidationParPoste('DIR_EXPLOI'));
        $this->assertSame(3, $service->getNiveauValidationParPoste('DIR_EXPLOITATION'));
        $this->assertSame(4, $service->getNiveauValidationParPoste('DIR_GENERA'));
        $this->assertSame(4, $service->getNiveauValidationParPoste('DIR_GENERAL'));
        $this->assertNull($service->getNiveauValidationParPoste('ADMIN'));
    }

    public function test_ordinary_employee_in_hr_service_starts_at_level_two(): void
    {
        $service = new WorkflowPermissionService();
        $employee = new \App\Models\Employe();
        $employee->setRelation('affectationActuelle', new class {
            public string $code_poste = 'EMPLOYE';
            public string $code_service = 'RH_SERV';
        });

        $this->assertSame('en_attente_n2', $service->getStatutInitial($employee));
    }
}
