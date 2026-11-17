<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthPagesTest extends TestCase
{
    public function test_login_page_is_available(): void
    {
        $this->get('/login')->assertOk()->assertSee('Sign in to NusaBoard');
    }

    public function test_registration_page_is_available(): void
    {
        $this->get('/register')->assertOk()->assertSee('Create your account');
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
