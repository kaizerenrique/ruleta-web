<?php

namespace Tests\Feature;

use Tests\TestCase;

class ObsoleteSpinEndpointTest extends TestCase
{
    public function test_removed_spin_endpoint_returns_404(): void
    {
        $this->post('/girar')->assertNotFound();
    }
}
