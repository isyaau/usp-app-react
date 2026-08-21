<?php

test('the application redirects root to login', function () {
    $response = $this->get('/');

    $response->assertStatus(302)->assertRedirect(route('login', absolute: false));
});

test('login page renders inertia app', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('id="app"', false);
});
