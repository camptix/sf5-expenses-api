<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\User;

use App\Tests\Functional\TestBase;

class UserTestBase extends TestBase
{
    protected String $endpoint;

    public function setUp()
    {
        parent::setUp();

        $this->endpoint = '/api/v1/users';
    }
}