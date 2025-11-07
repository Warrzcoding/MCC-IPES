<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminRouteAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_is_redirected_from_admin_route(): void
    {
        $student = User::create([
            'username' => 'student1',
            'email' => 'student1@example.com',
            'password' => Hash::make('password'),
            'full_name' => 'Student One',
            'role' => 'student',
            'status' => 'active'
        ]);

        $response = $this->actingAs($student)->get('/dashboard/academic-years');

        $response->assertRedirect(route('login'));
    }
}
