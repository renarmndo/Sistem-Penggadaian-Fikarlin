<?php

test('the application redirects root to login page', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
