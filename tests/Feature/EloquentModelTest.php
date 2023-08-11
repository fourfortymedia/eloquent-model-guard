<?php

namespace Fourfortymedia\Tests\Feature;


use Fourfortymedia\EloquentModelGuard\Attributes\OnCreateRules;
use Fourfortymedia\EloquentModelGuard\Attributes\OnUpdateRules;
use Fourfortymedia\EloquentModelGuard\Concerns\useEloquentModelGuard;
use Fourfortymedia\EloquentModelGuard\Exceptions\InvalidModelException;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcherContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Mockery;

/**
 *
 */
class EloquentModelTest extends \Orchestra\Testbench\TestCase
{

    use RefreshDatabase;

    /*
 * Bootstrap the application
 */
    /**
     * @var (\Eloquent&Mockery\LegacyMockInterface)|Mockery\LegacyMockInterface|\Eloquent|(\Eloquent&Mockery\MockInterface)|Mockery\MockInterface
     */
    private Mockery\LegacyMockInterface|Mockery\MockInterface|\Eloquent $mock;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->artisan = $this->app->make('Illuminate\Contracts\Console\Kernel');

        $this->artisan('migrate', [
                '--database' => 'sqllite',
                '--realpath' => realpath(__DIR__.'/../database/migrations'),
            ]
        )->run();
        Item::boot();

    }
    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function defineEnvironment($app): void
    {
        // Setup default database to use sqlite :memory:
        tap($app->make('config'), callback: function (Repository $config) {
            $config->set('database.default', 'sqllite');
            $config->set('database.connections.sqllite', [
                'driver'   => 'sqlite',
                'database' => ':memory:',
                'prefix'   => '',
            ]);
        });
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testBasicTest()
    {
        $mock = Mockery::mock(Item::class);
        $mock->shouldReceive('validate')->andThrow(InvalidModelException::class);
        $mock->shouldReceive('save')->andReturnUsing(function (Model $model) {
            $model->validate();
        });



        $this->app->instance(Item::class, $mock);
        $this->assertSame(0, 0);

        dd(Item::create([
            //'name' => 'test',
            'description' => 'test',
            'is_active' => 'yes'
            ]));


    }

}

/**
 * @property-read string $name
 */
#[OnCreateRules(['name' => 'required'])]
class Item extends Model
{
    use useEloquentModelGuard;
    #[OnUpdateRules('min:30')]
    protected string $name;
    protected $fillable = ['name', 'description', 'is_active'];

}