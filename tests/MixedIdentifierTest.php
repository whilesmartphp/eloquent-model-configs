<?php

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\TestCase;
use Whilesmart\ModelConfiguration\ConfigurationServiceProvider;
use Workbench\App\Models\User;

#[WithMigration]
class MixedIdentifierTest extends TestCase
{
    use DatabaseMigrations;

    protected function getPackageProviders($app): array
    {
        return [ConfigurationServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $database = env('DB_DATABASE', 'eloquent_model_configs_test');
        if (! in_array($database, ['eloquent_model_configs_test', 'db_test'], true)) {
            throw new LogicException('Mixed identifier tests require a dedicated MySQL test database.');
        }
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql.url', null);
        $app['config']->set('database.connections.mysql.unix_socket', '');
        foreach (['host' => env('DB_HOST', '127.0.0.1'), 'port' => env('DB_PORT', 3306),
            'database' => $database, 'username' => env('DB_USERNAME', 'root'), 'password' => env('DB_PASSWORD', '')] as $key => $value) {
            $app['config']->set('database.connections.mysql.'.$key, $value);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['uuid_configuration_owners', 'ulid_configuration_owners'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->string('id', 64)->primary();
                $table->string('name');
                $table->string('email');
                $table->string('password');
                $table->timestamps();
            });
        }
    }

    public function test_integer_uuid_and_ulid_models_share_configuration_storage(): void
    {
        $this->assertSame('mysql', DB::connection()->getDriverName());
        $models = [];
        foreach ([User::class, UuidConfigurationOwner::class, UlidConfigurationOwner::class] as $index => $class) {
            $model = $class::create(['name' => 'Owner', 'email' => 'owner'.$index.'@example.test', 'password' => 'test-password']);
            $this->actingAs($model)->postJson('/api/configurations', ['key' => 'theme', 'value' => 'owner-'.$index, 'type' => 'string'])
                ->assertCreated()->assertJsonPath('data.configurable_id', $model->id);
            $models[] = $model;
        }
        foreach ($models as $index => $model) {
            $this->actingAs($model)->getJson('/api/configurations/theme')->assertOk()
                ->assertJsonPath('data.value', 'owner-'.$index);
            $this->assertTrue($model->getConfig('theme')->configurable->is($model));
        }
    }

    public function test_upgrade_preserves_integer_references_and_allows_uuid_references(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->unsignedBigInteger('configurable_id')->change();
        });
        $owner = User::create(['name' => 'Owner', 'email' => 'owner@example.test', 'password' => 'test-password']);
        $this->actingAs($owner)->postJson('/api/configurations', ['key' => 'theme', 'value' => 'dark', 'type' => 'string'])->assertCreated();

        $migration = require __DIR__.'/../database/migrations/2026_11_25_000001_allow_string_configuration_references.php';
        $migration->up();

        $this->actingAs($owner)->getJson('/api/configurations/theme')->assertOk()->assertJsonPath('data.value', 'dark');
        $uuid = UuidConfigurationOwner::create(['name' => 'UUID owner', 'email' => 'uuid@example.test', 'password' => 'test-password']);
        $this->actingAs($uuid)->postJson('/api/configurations', ['key' => 'theme', 'value' => 'light', 'type' => 'string'])->assertCreated();
        $migration->down();
        $this->actingAs($uuid)->getJson('/api/configurations/theme')->assertOk()->assertJsonPath('data.value', 'light');
    }
}

class UuidConfigurationOwner extends User
{
    use HasUuids;

    protected $table = 'uuid_configuration_owners';
}

class UlidConfigurationOwner extends User
{
    use HasUlids;

    protected $table = 'ulid_configuration_owners';
}
