# 0001_01_01_000000_create_users_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50);
            $table->string('email', 100)->nullable();
            $table->string('image', 100)->nullable();
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->string('status', 10)->default('ACTIVE');
            $table->string('role_id')->nullable();
            $table->string('locale', 10)->default('en');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->integer('update_num')->default(0);
            $table->string('fcm_token')->nullable();
            $table->string('platform', 10)->nullable()->comment('IOS, ANDROID');
            $table->string('type', 50)->default('Mobile')->comment('Mobile, Admin, Station Admin, Card');
			$table->string('avatar_fallback_color')->nullable()->comment('blue', 'red');
			$table->string('language')->nullable()->comment('language');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

```

# 0001_01_01_000001_create_cache_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};

```

# 0001_01_01_000002_create_jobs_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};

```

# 2019_12_14_000001_create_personal_access_tokens_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->uuidMorphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestampTz('last_used_at')->nullable();
            $table->timestampTz('expires_at')->nullable();
            $table->timestampsTz();

            // New indexes for high-traffic auth operations
            $table->index(['tokenable_id', 'tokenable_type', 'expires_at']);
            $table->index(['token', 'expires_at']);
            $table->index(['name', 'tokenable_id', 'expires_at']);

            // Index for cleanup of expired tokens
            $table->index('expires_at');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};

```

# 2023_03_07_132707_create_permission_groups_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->integer('display_order');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permission_groups');
    }
};

```

# 2023_03_07_223526_create_roles_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50);
            $table->string('description')->nullable();
			$table->string('status', 10)->comment('ACTIVE, INACTIVE , DELETED')->default('ACTIVE');
			$table->string('type', 50)->comment('admin, developer, user')->default('user');
			$table->string('created_by')->nullable();
			$table->string('updated_by')->nullable();
			$table->integer('update_num')->default(0);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
};
```

# 2023_03_07_224659_create_permissions_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('module', 50)->comment('ADMIN, STATION, CHARGING POINT', 'CHARGING CONNECTOR');
            $table->string('name', 50)->comment('CREATE, READ, UPDATE, DELETE');
            $table->string('slug', 50)->comment('admin:create, admin:read, admin:update, admin:delete');
            $table->boolean('developer_only')->comment('1 = True / 2 = False')->default(0);
			$table->string('status', 10)->comment('ACTIVE, INACTIVE , DELETED')->default('ACTIVE');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
};

```

# 2023_03_07_224939_create_role_permissions_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuId('role_id')->references('id')->on('roles');
            $table->foreignUuId('permission_id')->nullable()->references('id')->on('permissions');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_permissions');
    }
};

```

# 2023_03_07_225241_create_settings_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('setting_key');
            $table->string('setting_value')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};

```

# 2023_03_15_093932_create_translations_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key' , 100);
            $table->json('value' , 300);
            $table->string('platform', 10)->comment('ADMIN, MOBILE')->default('ADMIN');
			$table->string('status', 10)->comment('ACTIVE, INACTIVE , DELETED')->default('ACTIVE');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translations');
    }
};

```

# 2023_03_29_110506_create_locales_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->string('code', 10);
            $table->string('iso', 10);
            $table->string('flag', 50)->nullable();
            $table->boolean('default')->comment('0 = False / 1 = True')->default(0);
			$table->string('status', 10)->comment('ACTIVE, INACTIVE , DELETED')->default('ACTIVE');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locales');
    }
};

```

# 2024_09_19_112933_create_user_register_o_t_p_s_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_register_o_t_p_s', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->string('transaction_code');
			$table->string('phone', 15);
			$table->string('country_code', 5);
			$table->string('otp', 6)->nullable();
			$table->string('status', 10)->comment('PENDING, EXPIRED, VERIFIED, FAILED');
			$table->timestampTz('expired_at');
			$table->integer('attempts')->default(0);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_register_o_t_p_s');
    }
};

```

# 2024_09_19_201424_create_password_reset_o_t_p_s_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('password_reset_o_t_p_s', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->string('transaction_code');
			$table->string('phone', 15);
			$table->string('country_code', 5);
			// $table->string('otp', 6);
			$table->string('status', 10)->comment('PENDING, EXPIRED, VERIFIED, FAILED');
			$table->timestampTz('expired_at');
			$table->integer('attempts')->default(0);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_o_t_p_s');
    }
};

```

# 2024_09_21_114433_create_payment_methods_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name',100); // Payment method name, e.g., ABA Bank, Credit Card
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('type', 50)->comment('online,cash,card_on_delivery');

            $table->string('status', 10)->default('ACTIVE')->comment('ACTIVE, INACTIVE');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->integer('update_num')->default(0);
            $table->timestampsTz();
            $table->softDeletesTz(); // Adds a nullable `deleted_at` timestamp with timezone
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
```

# 2024_09_23_184037_create_faqs_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->json('question');
            $table->json('answer');
			// $table->string('category')->comment('FAQ, ABOUT, SUPPORT');
            $table->string('platform', 10)->default('MOBILE');
			$table->string('image')->nullable();
            $table->string('status', 10)->default('ACTIVE')->comment('ACTIVE, INACTIVE');
			$table->string('created_by')->nullable();
			$table->string('updated_by')->nullable();
			$table->integer('update_num')->default(0);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};

```

# 2024_09_23_192957_create_notifications_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->json('title');
			$table->json('message');
			$table->string('image')->nullable();
			$table->string('type', 20)->comment('PROMOTION, TRANSACTION, ALERT');
			$table->string('status', 10)->default('UNREAD')->comment('UNREAD, READ');
			$table->uuid('customer_id')->nullable();

			$table->timestampTz('read_at')->nullable();
			$table->boolean('is_read')->default(false);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

```

# 2024_09_23_193337_create_announcements_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->json('title');
			$table->json('message');
			$table->string('type', 20)->comment('PROMOTION, SYSTEM_ALERT, UPDATE');
			$table->timestampTz('scheduled_at')->nullable();
			$table->string('status', 10)->default('PENDING')->comment('PENDING, SENT, CANCELLED');
			$table->string('image')->nullable();
			$table->timestampTz('sent_at')->nullable();
			$table->string('sent_by')->nullable();
			$table->string('created_by')->nullable();
			$table->string('updated_by')->nullable();
			$table->integer('update_num')->default(0);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};

```

# 2024_11_10_143111_create_user_logins_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_logins', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->string('type', 10)->comment('login, logout');
			$table->uuid('user_id')->index();
			$table->string('ip_address');
			$table->string('browser')->nullable()->comment('Chrome, Firefox, Safari, Edge');
			$table->timestampTz('login_at')->nullable();
			$table->timestampTz('logout_at')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_logins');
    }
};

```

# 2024_11_19_140744_create_audits_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = config('audit.drivers.database.connection', config('database.default'));
        $table = config('audit.drivers.database.table', 'audits');


        Schema::connection($connection)->create($table, function (Blueprint $table) {
			DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
			$morphPrefix = config('audit.user.morph_prefix');
			$table->string('id')->default(DB::raw('uuid_generate_v4()'))->primary();

			$table->string($morphPrefix . '_type')->nullable();
			$table->uuid($morphPrefix . '_id')->nullable();
			$table->index([
				$morphPrefix . '_type',
				$morphPrefix . '_id',
			]);

            $table->string('event');

			$table->string('auditable_type');
			$table->uuid('auditable_id');
			$table->index([
				'auditable_type',
				'auditable_id',
			]);

            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->text('url')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 1023)->nullable();
            $table->string('tags')->nullable();
            $table->timestampsTz();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = config('audit.drivers.database.connection', config('database.default'));
        $table = config('audit.drivers.database.table', 'audits');

        Schema::connection($connection)->drop($table);
    }
}

```

# 2024_11_20_152737_create_static_contents_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('static_contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->json('title');
			$table->json('content');
			$table->string('image')->nullable();
			$table->string('type', 20)->comment('PRIVACY_POLICY, TERMS_AND_CONDITIONS, ABOUT_US');
			$table->string('status', 10)->default('ACTIVE')->comment('ACTIVE, INACTIVE, DELETED');
			$table->string('created_by')->nullable();
			$table->string('updated_by')->nullable();
			$table->integer('update_num')->default(0);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('static_contents');
    }
};

```

# 2024_12_03_141904_create_refresh_tokens_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('name');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('token', 64)->unique();
            $table->timestampTz('expires_at');
            $table->boolean('revoked')->default(false);
            $table->timestampsTz();

            // New indexes for refresh token operations
            $table->index(['user_id', 'revoked', 'expires_at']);
            $table->index(['token', 'revoked', 'expires_at']);

            // For active tokens, use a simpler partial index
            $table->index(['token', 'user_id'])->where('revoked', false);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refresh_tokens');
    }
};

```

# 2024_12_27_170749_create_app_versions_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->uuid('announcement_id')->nullable();
            $table->string('platform')->comment('IOS, ANDROID');
            $table->string('latest_version', 10);  // Latest app version (e.g., '2.0.1')
            $table->text('update_url')->nullable();  // URL to the app store for updates
            $table->boolean('force_update')->default(false);  // Indicates if the update is mandatory
            $table->text('message')->nullable();  // Optional message to display to the user
            $table->timestampsTz();  // 'created_at' and 'updated_at' timestampsTz
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
};

```

# 2025_05_27_060354_create_telescope_entries_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Get the migration connection name.
     */
    public function getConnection(): ?string
    {
        return config('telescope.storage.database.connection');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $schema = Schema::connection($this->getConnection());

        $schema->create('telescope_entries', function (Blueprint $table) {
            $table->bigIncrements('sequence');
            $table->uuid('uuid');
            $table->uuid('batch_id');
            $table->string('family_hash')->nullable();
            $table->boolean('should_display_on_index')->default(true);
            $table->string('type', 20);
            $table->longText('content');
            $table->dateTime('created_at')->nullable();

            $table->unique('uuid');
            $table->index('batch_id');
            $table->index('family_hash');
            $table->index('created_at');
            $table->index(['type', 'should_display_on_index']);
        });

        $schema->create('telescope_entries_tags', function (Blueprint $table) {
            $table->uuid('entry_uuid');
            $table->string('tag');

            $table->primary(['entry_uuid', 'tag']);
            $table->index('tag');

            $table->foreign('entry_uuid')
                ->references('uuid')
                ->on('telescope_entries')
                ->onDelete('cascade');
        });

        $schema->create('telescope_monitoring', function (Blueprint $table) {
            $table->string('tag')->primary();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $schema = Schema::connection($this->getConnection());

        $schema->dropIfExists('telescope_entries_tags');
        $schema->dropIfExists('telescope_entries');
        $schema->dropIfExists('telescope_monitoring');
    }
};

```

# 2025_09_14_160943_create_general_settings_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('general_settings', function (Blueprint $table) {
			$table->uuid('id')->primary();
            $table->string('key')->unique()->comment('The machine-readable key for the setting.');
            $table->string('name')->comment('A human-readable name for the setting.');
            $table->text('value')->nullable()->comment('The value of the setting.');
            $table->string('type')->default('string')->comment('Input type for the admin panel: string, text, boolean, etc.');
            $table->string('group')->default('Default')->index()->comment('A name to group related settings in the UI.');
            $table->text('description')->nullable()->comment('A helpful explanation of the setting.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
```

# 2025_10_08_090121_create_table_products.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ----- BRANDS -----
        Schema::create('brands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // ----- CATEGORIES (e.g. EV, Gasoline, Hybrid) -----
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // ----- LISTING TYPES (e.g. Car, Bike, Boat) -----
        Schema::create('listing_types', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // ----- VEHICLE LISTINGS -----
        Schema::create('listings', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            
            // All foreign keys referencing a UUID primary key must use foreignUuid()
            $table->foreignUuid('seller_id')->constrained('users')->cascadeOnDelete();
            
            // Foreign keys referencing UUIDs in new tables
            $table->foreignUuid('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignUuid('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignUuid('listing_type_id')->nullable()->constrained('listing_types')->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->string('location')->nullable();
            $table->enum('condition', ['new', 'used'])->default('used');
            $table->enum('status', ['pending', 'approved', 'rejected', 'sold'])->default('pending');

            $table->timestamps();
        });

        // ----- SHOP PRODUCT TYPES (e.g. Car Parts, Accessories) -----
        Schema::create('shop_product_types', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // ----- SHOP PRODUCTS (orderable small items) -----
        Schema::create('shop_products', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('stock')->default(0);
            
            // Foreign keys referencing UUIDs
            $table->foreignUuid('seller_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignUuid('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignUuid('type_id')->nullable()->constrained('shop_product_types')->nullOnDelete();
            
            $table->string('image_url')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // ----- ADDRESSES -----
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete(); // CHANGED to foreignUuid
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country');
            $table->string('phone_number');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // ----- ORDERS -----
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->foreignUuid('buyer_id')->constrained('users')->cascadeOnDelete(); 
            $table->decimal('total_price', 12, 2);
            $table->foreignUuid('shipping_address_id')->nullable()->constrained('addresses')->nullOnDelete();
            
            $table->string('shipping_phone')->nullable(); 
            $table->enum('status', ['pending', 'paid', 'shipped', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        // ----- ORDER ITEMS -----
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->foreignUuid('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignUuid('shop_product_id')->constrained('shop_products')->cascadeOnDelete();
            
            $table->integer('quantity');
            $table->decimal('price', 12, 2);
            $table->timestamps();
        });

        // ----- PAYMENTS -----
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->foreignUuid('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete(); 
            
            $table->decimal('amount', 12, 2);
            $table->string('method')->nullable();
            $table->enum('purpose', ['order', 'listing_fee', 'feature_fee'])->default('order');
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('shop_products');
        Schema::dropIfExists('shop_product_types');
        Schema::dropIfExists('listings');
        Schema::dropIfExists('listing_types');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('brands');
    }
};
```

# 2025_10_15_121029_create_stores_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('logo_url')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('zip_code');
            $table->string('country')->default('DefaultCountry');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('phone_number')->nullable();
            $table->string('telegram')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('license_number')->nullable()->unique();
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->boolean('is_24_hours')->default(false);
            $table->boolean('delivers_product')->default(false);
            $table->text('delivery_details')->nullable(); // e.g., radius, fees
            $table->decimal('average_rating', 2, 1)->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->boolean('is_highlighted')->default(false);
            $table->boolean('is_top_choice')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
            $table->softDeletes();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
```

# 2025_10_29_114244_create_store_notification_settings_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This table stores notification channel settings for each store,
     * allowing a store to have multiple notification providers (e.g., Telegram, Slack).
     */
    public function up(): void
    {
        Schema::create('store_notification_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Link to the store
            $table->foreignUuid('store_id')->constrained('stores')->onDelete('cascade');

            $table->enum('provider', ['telegram', 'email', 'facebook'])
                ->comment("The notification service provider: 'telegram', 'email', or 'facebook'");

            $table->string('name', 255)
                ->comment('A friendly name for this configuration, e.g., "Sales Alerts Chat" or "Order Notifications"');


            // Store provider-specific details like bot_token, chat_id, webhook_url, etc.
            $table->json('credentials')
                ->comment('Provider-specific configuration stored as JSON');


            $table->boolean('is_active')
                ->default(true)
                ->comment('Toggle to enable or disable this specific notification channel');


            $table->timestamps();

            // Add indexes for better performance
            $table->index(['store_id', 'provider']);
            $table->index(['store_id', 'is_active']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_notification_settings');
    }
};
```

# 2025_10_31_150230_create_content_blocks_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); 
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->json('booking_btn')->nullable();
            $table->text('image_path')->nullable();
            $table->string('status', 10)->comment('ACTIVE, INACTIVE , DELETED')->default('ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_blocks');
    }
};
```

# 2025_11_10_135356_create_service_cards_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_cards', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('description');
            $table->json('button_text');
            $table->string('image_url');
            $table->string('status', 10)->comment('ACTIVE, INACTIVE , DELETED')->default('ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_cards');
    }
};
```

# 2025_11_24_142200_conversations.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('last_message_at')->nullable()->index();
            $table->foreignUuid('pharmacy_id')
                  ->nullable()
                  ->constrained('pharmacies')
                  ->onDelete('cascade');
				  
			$table->enum('type', ['DOCTOR_PATIENT', 'PHARMACIST_PATIENT'])->default('DOCTOR_PATIENT')->after('id');

            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
```

# 2025_11_24_142401_messages.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
			$table->uuid('id')->primary();
            $table->foreignUuid('conversation_id')->constrained('conversations')->onDelete('cascade');
            $table->foreignUuid('sender_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->json('content');

            // To handle different types of messages, e.g., text, image, file, system message.
            $table->string('type')->default('text');

            $table->timestamp('read_at')->nullable();

			$table->foreignUuid('reply_to_message_id')->nullable()->onDelete('set null');
            
            // For tracking edits
            $table->timestamp('edited_at')->nullable();


            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();

            $table->index('conversation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
```

# 2025_11_24_142432_message_reactions.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('message_reactions', function (Blueprint $table) {
			$table->uuid('id')->primary();
            $table->foreignUuid('message_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('emoji', 16); // Store the emoji character
            $table->timestamps();

            // A user can only react with the same emoji once per message
            $table->unique(['message_id', 'user_id', 'emoji']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_reactions');
    }
};
```

# 2025_11_24_142459_message_read_statuses.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('message_read_statuses', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->foreignUuid('message_id')->constrained()->onDelete('cascade');
			$table->foreignUuid('user_id')->constrained()->onDelete('cascade');
			$table->timestamp('read_at');
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_read_statuses');
    }
};
```

# 2025_11_24_142735_conversation_participants.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->foreignUuid('conversation_id')->constrained('conversations')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->unique(['conversation_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_participants');
    }
};
```

# 2025_11_24_142822_conversation_participants.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('conversation_participants', function (Blueprint $table) {
            // Add this line
            $table->timestamp('last_read')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversation_participants', function (Blueprint $table) {
            // This allows you to undo the migration if needed
            $table->dropColumn('last_read');
        });
    }
};
```

