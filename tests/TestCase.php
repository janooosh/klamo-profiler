<?php

namespace Klamo\ProfilingSystem\Tests;

use Klamo\ProfilingSystem\ProfilingServiceProvider;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestCase extends \Orchestra\Testbench\TestCase
{
  use RefreshDatabase;
  
  public function setUp(): void
  {
    parent::setUp();
    // additional setup
    $this->loadMigrationsFrom(realpath(__DIR__ . '/../database/migrations'));
    $this->artisan('migrate', [
    '--path' => realpath(__DIR__ . '/../database/migrations'),
    '--realpath' => true,
    '--database' => 'testing'
    ]);
  }

  protected function getPackageProviders($app)
  {
    return [
      // setup the service provider for this package
      ProfilingServiceProvider::class,
    ];
  }

  protected function getEnvironmentSetUp($app)
  {
    // Setup default database to use sqlite :memory:
    $app['config']->set('database.default', 'testbench');
    $app['config']->set('database.connections.testbench', [
        'driver'   => 'sqlite',
        'database' => ':memory:',
        'prefix'   => '',
    ]);
  }
}