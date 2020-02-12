<?php


namespace Dababo\LazyChunk\Tests;


use Dababo\LazyChunk\LazyChunkServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['db']->connection()
            ->getSchemaBuilder()
            ->dropAllTables();

        $this->withFactories(__DIR__ . '/factories');

    }

    protected function getPackageProviders($app)
    {
        return [
            LazyChunkServiceProvider::class,
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

    protected function createArticleSchema()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');

            $table->timestamps();
        });
    }
}