<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteConfigurationTest extends TestCase
{
    public function test_password_routes_have_distinct_names_and_targets(): void
    {
        $this->assertSame('/reinitialiser-mot-de-passe', route('password.update', [], false));
        $this->assertSame('/password/update', route('password.change', [], false));
    }

    public function test_locality_resource_uses_the_model_code_as_its_route_parameter(): void
    {
        $this->assertSame(
            '/localites/LOC001/edit',
            route('localites.edit', ['code_localite' => 'LOC001'], false)
        );
    }

    public function test_planning_show_route_accepts_an_employee_matricule(): void
    {
        $this->assertSame(
            '/plannings/EMP001',
            route('plannings.show', ['matricule_employe' => 'EMP001'], false)
        );

        $route = Route::getRoutes()->getByName('plannings.show');
        $this->assertSame('show', $route->getActionMethod());
        $this->assertSame(['matricule_employe'], $route->parameterNames());
    }

    public function test_employment_history_show_route_accepts_an_employee_matricule(): void
    {
        $this->assertSame(
            '/historique_postes/EMP001',
            route('historique_postes.show', ['matricule_employe' => 'EMP001'], false)
        );
    }

    public function test_maintenance_route_is_available_without_authentication(): void
    {
        $route = Route::getRoutes()->getByName('maintenance.licence');

        $this->assertNotContains('auth', $route->gatherMiddleware());
    }

    public function test_password_and_locality_routes_have_their_required_views(): void
    {
        $this->assertTrue(view()->exists('auth.passwords.edit'));
        $this->assertTrue(view()->exists('localites.show'));
    }
}
