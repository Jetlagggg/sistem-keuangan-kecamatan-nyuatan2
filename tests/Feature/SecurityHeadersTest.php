<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_security_headers_are_present_on_a_normal_response(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        $this->assertStringContainsString(
            "default-src 'self'",
            $response->headers->get('Content-Security-Policy')
        );
        $this->assertStringContainsString(
            "frame-ancestors 'self'",
            $response->headers->get('Content-Security-Policy')
        );
    }

    public function test_security_headers_are_present_on_a_redirect_response(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }
}
