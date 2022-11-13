<?php

namespace Jonreyg\LaravelRedisManager\Tests;

use Jonreyg\LaravelRedisManager\LaravelRedisManagerServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
  public function setUp(): void
  {
    parent::setUp();
    // additional setup
  }

  protected function getPackageProviders($app)
  {
    return [
        LaravelRedisManagerServiceProvider::class,
    ];
  }

  protected function getEnvironmentSetUp($app)
  {
    // perform environment setup
  }
}
