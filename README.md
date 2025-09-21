# Filament Activity History

[![Latest Version on Packagist](https://img.shields.io/packagist/v/nanorocks/filament-activity-history.svg?style=flat-square)](https://packagist.org/packages/nanorocks/filament-activity-history)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/nanorocks/filament-activity-history/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/nanorocks/filament-activity-history/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/nanorocks/filament-activity-history/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/nanorocks/filament-activity-history/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/nanorocks/filament-activity-history.svg?style=flat-square)](https://packagist.org/packages/nanorocks/filament-activity-history)

A comprehensive activity logging plugin for Filament v3 that provides detailed user activity tracking and history management. Built on top of the powerful [Spatie Laravel Activity Log](https://github.com/spatie/laravel-activitylog) package, this plugin seamlessly integrates activity tracking into your Filament admin panels.

Track user actions, model changes, and custom activities with automatic logging, customizable retention policies, and easy-to-use management commands. Perfect for compliance, auditing, and debugging purposes in your Filament applications.

## Requirements

- PHP 8.1 or higher
- Laravel 10.0 or higher  
- Filament 3.0 or higher

## Installation

Install the package via Composer:

```bash
composer require nanorocks/filament-activity-history
```

### Quick Setup

For a quick setup with default configuration, run the install command:

```bash
php artisan filament-activity-history:install
```

This command will:
- Publish the configuration file
- Publish and run the migrations  
- Optionally ask to star the repository on GitHub

### Manual Setup

If you prefer manual setup or need more control:

1. **Publish and run migrations:**
   ```bash
   php artisan vendor:publish --tag="filament-activity-history-migrations"
   php artisan migrate
   ```

2. **Publish configuration file:**
   ```bash
   php artisan vendor:publish --tag="filament-activity-history-config"
   ```

3. **Optionally publish views for customization:**
   ```bash
   php artisan vendor:publish --tag="filament-activity-history-views"
   ```

4. **Optionally publish language files:**
   ```bash
   php artisan vendor:publish --tag="filament-activity-history-translations"
   ```

## Plugin Registration

Add the plugin to your Filament panel configuration. In your `AdminPanelProvider` or similar panel provider:

```php
<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Nanorocks\FilamentActivityHistory\FilamentActivityHistoryPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            // ... other panel configuration
            ->plugins([
                FilamentActivityHistoryPlugin::make(),
                // ... other plugins
            ]);
    }
}
```

### Alternative Registration Methods

You can also register the plugin using any of these methods:

```php
// Using the make() method
->plugins([
    FilamentActivityHistoryPlugin::make(),
])

// Using the get() method if already instantiated
->plugins([
    FilamentActivityHistoryPlugin::get(),
])
```

## Configuration

The plugin uses a configuration file that allows you to customize various aspects of activity logging. After publishing the config file, you'll find it at `config/activity-history.php`:

```php
<?php

return [
    /*
     * If set to false, no activities will be saved to the database.
     */
    'enabled' => env('ACTIVITY_HISTORY_ENABLED', true),

    /*
     * When the clean-command is executed, all recording activities older than
     * the number of days specified here will be deleted.
     */
    'delete_records_older_than_days' => 365,

    /*
     * If no log name is passed to the activity() helper
     * we use this default log name.
     */
    'default_log_name' => 'default',

    /*
     * You can specify an auth driver here that gets user models.
     * If this is null we'll use the current Laravel auth driver.
     */
    'default_auth_driver' => null,

    /*
     * If set to true, the subject returns soft deleted models.
     */
    'subject_returns_soft_deleted_models' => false,

    /*
     * This model will be used to log activity.
     * It should implement the Spatie\Activitylog\Contracts\Activity interface
     * and extend Illuminate\Database\Eloquent\Model.
     */
    'activity_model' => \Nanorocks\FilamentActivityHistory\Models\Activity::class,

    /*
     * This is the name of the table that will be created by the migration and
     * used by the Activity model shipped with this package.
     */
    'table_name' => env('ACTIVITY_HISTORY_TABLE_NAME', 'filament_activity_history'),

    /*
     * This is the database connection that will be used by the migration and
     * the Activity model shipped with this package. In case it's not set
     * Laravel's database.default will be used instead.
     */
    'database_connection' => env('ACTIVITY_LOGGER_DB_CONNECTION'),
];
```

### Environment Variables

You can override configuration values using environment variables in your `.env` file:

```env
ACTIVITY_HISTORY_ENABLED=true
ACTIVITY_HISTORY_TABLE_NAME=filament_activity_history
ACTIVITY_LOGGER_DB_CONNECTION=mysql
```

## Usage

This plugin is built on top of [Spatie Laravel Activity Log](https://spatie.be/docs/laravel-activitylog), so you can use all of its features and methods.

### Basic Activity Logging

#### Automatic Model Activity Logging

To automatically log activities for your Eloquent models, add the `LogsActivity` trait:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Post extends Model
{
    use LogsActivity;

    protected $fillable = ['title', 'content', 'published'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'content', 'published'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
```

#### Manual Activity Logging

Log custom activities anywhere in your application:

```php
use Spatie\Activitylog\Facades\CauserResolver;
use Spatie\Activitylog\Facades\LogBatch;

// Log a simple activity
activity()
    ->log('User viewed dashboard');

// Log with additional properties
activity()
    ->withProperties(['ip' => request()->ip()])
    ->log('User logged in');

// Log activity for a specific model
activity()
    ->performedOn($post)
    ->log('Post was viewed');

// Log activity with a specific causer
activity()
    ->causedBy($user)
    ->performedOn($post)
    ->withProperties(['from' => 'admin-panel'])
    ->log('Post was updated');
```

#### Filament Resource Integration

Automatically log activities for your Filament resources:

```php
<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use App\Models\Post;
use Spatie\Activitylog\Traits\LogsActivity;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    
    // The model should use LogsActivity trait
    // Activities will be automatically logged for create, update, delete actions
}
```

### Viewing Activity Logs

#### Retrieve Activities

```php
// Get all activities
$activities = \Nanorocks\FilamentActivityHistory\Models\Activity::all();

// Get activities for a specific model
$activities = \Nanorocks\FilamentActivityHistory\Models\Activity::forSubject($post)->get();

// Get activities by a specific user
$activities = \Nanorocks\FilamentActivityHistory\Models\Activity::causedBy($user)->get();

// Get recent activities
$recentActivities = \Nanorocks\FilamentActivityHistory\Models\Activity::latest()->take(10)->get();
```

#### Activity Properties

Each activity log contains the following information:

```php
$activity = \Nanorocks\FilamentActivityHistory\Models\Activity::first();

echo $activity->description;        // The description of the activity
echo $activity->subject_type;       // The class name of the model
echo $activity->subject_id;         // The ID of the model
echo $activity->causer_type;        // The class name of the user model
echo $activity->causer_id;          // The ID of the user
echo $activity->properties;         // Additional properties (JSON)
echo $activity->created_at;         // When the activity was logged
```

### Management Commands

#### Clean Old Activities

Remove old activity records based on the retention policy:

```bash
# Clean using the configured retention days
php artisan filament-activity-history-clear

# Or use the underlying Spatie command directly
php artisan activitylog:clean

# Clean activities older than specific days
php artisan activitylog:clean --days=30
```

## Publishing Vendor Assets for Customization

If you need to modify or extend the plugin's functionality, you can publish various vendor assets:

### Available Publishable Assets

#### 1. Configuration File
```bash
php artisan vendor:publish --tag="filament-activity-history-config"
```
**When to publish:** When you need to modify default settings, change table names, or customize activity logging behavior.

#### 2. Migration Files
```bash
php artisan vendor:publish --tag="filament-activity-history-migrations"
```
**When to publish:** When you need to modify the database schema, add custom columns, or change table structure.

#### 3. View Files
```bash
php artisan vendor:publish --tag="filament-activity-history-views"
```
**When to publish:** When you need to customize the appearance of activity logs or create custom activity display components.

#### 4. Translation Files
```bash
php artisan vendor:publish --tag="filament-activity-history-translations"
```
**When to publish:** When you need to translate the plugin into other languages or customize text labels.

#### 5. Stub Files (for development)
```bash
php artisan vendor:publish --tag="filament-activity-history-stubs"
```
**When to publish:** When you're developing custom extensions or need template files for creating custom activity loggers.

### Extending the Plugin

#### Custom Activity Model

If you need to extend the activity model with additional functionality:

1. Create your custom activity model:
```php
<?php

namespace App\Models;

use Nanorocks\FilamentActivityHistory\Models\Activity as BaseActivity;

class CustomActivity extends BaseActivity
{
    // Add custom methods or properties
    public function getFormattedPropertiesAttribute()
    {
        return json_decode($this->properties, true);
    }
    
    // Add custom relationships
    public function customRelation()
    {
        return $this->belongsTo(SomeModel::class);
    }
}
```

2. Update your configuration to use the custom model:
```php
// config/activity-history.php
'activity_model' => \App\Models\CustomActivity::class,
```

#### Custom Service Provider

For advanced customizations, you can extend the service provider:

```php
<?php

namespace App\Providers;

use Nanorocks\FilamentActivityHistory\FilamentActivityHistoryServiceProvider as BaseServiceProvider;

class CustomActivityHistoryServiceProvider extends BaseServiceProvider
{
    public function packageBooted(): void
    {
        parent::packageBooted();
        
        // Add your custom logic here
    }
}
```

Then register your custom provider in `config/app.php` instead of the base provider.

### Customization Examples

#### Custom Activity Resource

Create a custom Filament resource to manage activities:

```php
<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Nanorocks\FilamentActivityHistory\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'System';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description'),
                TextColumn::make('subject_type'),
                TextColumn::make('causer.name')->label('User'),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('subject_type')
                    ->options([
                        'App\\Models\\User' => 'User',
                        'App\\Models\\Post' => 'Post',
                        // Add your models here
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
```

#### Custom Activity Widget

Create a dashboard widget to display recent activities:

```php
<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Nanorocks\FilamentActivityHistory\Models\Activity;

class RecentActivityWidget extends Widget
{
    protected static string $view = 'filament.widgets.recent-activity';
    
    protected function getViewData(): array
    {
        return [
            'activities' => Activity::latest()->take(10)->get(),
        ];
    }
}
```

## Advanced Usage

### Batch Activities

Group related activities together:

```php
use Spatie\Activitylog\Facades\LogBatch;

LogBatch::startBatch();

activity()->log('First activity');
activity()->log('Second activity');
activity()->log('Third activity');

LogBatch::endBatch();
```

### Activity Properties and Changes

Log detailed changes with before/after values:

```php
// In your model's getActivitylogOptions method
public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->logAll()  // Log all attributes
        ->logExcept(['updated_at'])  // Except these
        ->logOnlyDirty()  // Only log changed attributes
        ->dontSubmitEmptyLogs()  // Skip if nothing changed
        ->useLogName('posts');  // Custom log name
}
```

### Conditional Logging

Control when activities are logged:

```php
// In your model
public function shouldLogActivity(): bool
{
    return auth()->check() && !app()->runningInConsole();
}
```

### Custom Activity Descriptions

Create dynamic activity descriptions:

```php
// In your model
public function getDescriptionForEvent(string $eventName): string
{
    return match($eventName) {
        'created' => "Post '{$this->title}' was created",
        'updated' => "Post '{$this->title}' was updated", 
        'deleted' => "Post '{$this->title}' was deleted",
        default => $eventName,
    };
}
```

## Troubleshooting

### Common Issues

#### Migration Errors
If you encounter migration errors:
```bash
# Check if the table already exists
php artisan tinker
>>> Schema::hasTable('filament_activity_history');

# If it exists, you may need to rollback and re-run
php artisan migrate:rollback --step=1
php artisan migrate
```

#### Configuration Not Loading
Ensure you've published and cleared the config cache:
```bash
php artisan vendor:publish --tag="filament-activity-history-config" --force
php artisan config:clear
```

#### Plugin Not Appearing
Make sure the plugin is properly registered in your panel provider and that you've cleared the cache:
```bash
php artisan filament:clear-cached-components
php artisan cache:clear
```

### Debug Mode

Enable debug logging in your `.env` file:
```env
LOG_LEVEL=debug
ACTIVITY_HISTORY_ENABLED=true
```

### Performance Considerations

For high-traffic applications:

1. **Use queued activity logging:**
```php
// In your model
public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->useLogName('queued')
        ->dontSubmitEmptyLogs();
}
```

2. **Regular cleanup:**
Set up a scheduled task to clean old activities:
```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('filament-activity-history-clear')
             ->daily();
}
```

3. **Database indexing:**
Add custom indexes for better query performance if needed.

## Best Practices

### 1. Selective Logging
Don't log everything - be selective about what you track:
```php
public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->logOnly(['title', 'status', 'published_at'])  // Only important fields
        ->logOnlyDirty()  // Only when changed
        ->dontSubmitEmptyLogs();  // Skip empty logs
}
```

### 2. Use Descriptive Log Names
Organize activities with meaningful log names:
```php
activity('admin-actions')->log('User created post');
activity('user-interactions')->log('User viewed post');
```

### 3. Include Context
Add relevant context to activities:
```php
activity()
    ->withProperties([
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'route' => request()->route()->getName(),
    ])
    ->log('User action performed');
```

### 4. Regular Maintenance
Set up automated cleanup to maintain performance:
```php
// Clean activities older than 1 year monthly
$schedule->command('filament-activity-history-clear')->monthly();
```

### 5. Monitor Storage
Keep an eye on database size and adjust retention policies as needed.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Andrej Nankov](https://github.com/nanorocks)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
