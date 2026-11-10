<?php

namespace Tests\Feature;

use App\Http\Requests\StoreDemandeRequest;
use App\Models\Employe;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class StoreDemandeAuthorizationTest extends TestCase
{
    public function test_admin_is_authorized_to_submit_a_demand_for_any_employee(): void
    {
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->twice()->andReturn($this->user('ADMIN001', true));

        $request = new StoreDemandeRequest;

        $this->assertTrue($request->authorize());
        $this->assertTrue($request->canSubmitFor('EMPLOYEE1'));
    }

    public function test_responsable_of_the_rh_service_can_submit_for_any_employee(): void
    {
        Auth::shouldReceive('user')->once()->andReturn($this->user('RH001', true));

        $this->assertTrue((new StoreDemandeRequest)->canSubmitFor('EMPLOYEE1'));
    }

    public function test_responsable_of_another_service_can_only_submit_for_themselves(): void
    {
        Auth::shouldReceive('user')->twice()->andReturn($this->user('RESP001', false));

        $request = new StoreDemandeRequest;

        $this->assertTrue($request->canSubmitFor('RESP001'));
        $this->assertFalse($request->canSubmitFor('EMPLOYEE1'));
    }

    public function test_rh_job_code_outside_the_rh_service_does_not_grant_bulk_submission(): void
    {
        Auth::shouldReceive('user')->once()->andReturn($this->user('RH001', false, 'RH'));

        $this->assertFalse((new StoreDemandeRequest)->canSubmitFor('EMPLOYEE1'));
    }

    public function test_unknown_post_is_not_authorized_to_submit_a_demand(): void
    {
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->once()->andReturn($this->user('VISITEUR1', false, 'VISITEUR'));

        $this->assertFalse((new StoreDemandeRequest)->authorize());
    }

    private function user(string $matricule, bool $canSubmitForOthers, string $codePoste = 'RESPONSABLE'): Employe
    {
        $user = \Mockery::mock(Employe::class)->makePartial();
        $user->setAttribute('matricule_employe', $matricule);
        $user->shouldReceive('codePosteActif')->andReturn($codePoste);
        $user->shouldReceive('peutCreerDemandePourTous')->andReturn($canSubmitForOthers);

        return $user;
    }
}
