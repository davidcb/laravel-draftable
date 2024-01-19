<?php

namespace Davidcb\LaravelDraftable\Test;

use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function setUpDatabase()
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('dummies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
        });

        $this->app['db']->connection()->getSchemaBuilder()->create('drafts', function (Blueprint $table) {
            $table->id();
            $table->json('draftable_data');
            $table->string('draftable_model');
            $table->unsignedBigInteger('draftable_id')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        collect(range(1, 10))->each(function (int $i) {
            Dummy::create([
                'title' => 'Title for item ' . $i,
            ]);
        });
    }
}
