# Resizable Columns

The **Resizable Columns** plugin allows you to resize table columns in Filament with persistent width settings. This package provides a seamless way to customize table layouts by letting users adjust column widths according to their preferences.

## Features
- Drag-to-resize column functionality
- Persistent column width settings
- Per-user width preferences
- Session and database storage options
- Easy integration with existing Filament tables
- Customizable storage mechanisms

## Installation
You can install the package via composer:

```bash
composer require asmit/resized-column
```

## Publishing Assets
```bash
# Publish migrations
php artisan vendor:publish --provider="Asmit\ResizedColumn\ResizedColumnServiceProvider" --tag=resized-column-migrations

# Run migrations
php artisan migrate

# Publish assets
php artisan filament:assets
```

## Registering the Plugin

Add the plugin to your Filament panel configuration in `app/Providers/Filament/AdminPanelProvider.php`:

```php
use Asmit\ResizedColumn\ResizedColumnPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... other configuration
        ->plugins([
            // ... other plugins
            ResizedColumnPlugin::make()
                ->preserveOnDB() // Enable database storage (optional)
                ->preserveOnSession() // Enable session storage (enabled by default)
        ]);
}
```

## Usage
```php
use Asmit\ResizedColumn\HasResizableColumn;

class ListUsers extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = UserResource::class;
    
    // Your existing table definition...
}
```

## Storage Configuration

The package provides two storage mechanisms:

1. **Session Storage** (Enabled by default)
   - Stores column widths in the user's session
   - No database required
   - Storage is browser/device specific

2. **Database Storage** (Optional)
   - Stores column widths in the database
   - Requires migration to create the `table_settings` table
   - Works across browsers/devices for the same user

You can enable or disable these options in your panel configuration:

```php
ResizedColumnPlugin::make()
    ->preserveOnDB(true) // Enable database storage
    ->preserveOnSession(true) // Enable session storage
```

## Configuration Options

You can override any of the following methods in your class to customize behavior:

| Method | Description |
|--------|-------------|
| `persistColumnWidthsToDatabase()` | Customize how column widths are saved to database |
| `persistColumnWidthsToSession()` | Customize how column widths are saved to session |
| `loadColumnWidthsFromDatabase()` | Customize how column widths are loaded from database |
| `loadColumnWidthsFromSession()` | Customize how column widths are loaded from session |
| `getUserId()` | Customize how user identification is handled |

## Example: Custom Database Storage

```php
use Asmit\ResizedColumn\HasResizableColumn;

class ListUsers extends ListRecords
{
    use HasResizableColumn;
    
    protected function persistColumnWidthsToDatabase(): void
    {
        // Your custom database save logic here
        YourCustomModel::updateOrCreate(
            [
                'user_id' => $this->getUserId(),
                'resource' => $this->getResourceModelFullPath(),
            ],
            ['settings' => $this->columnWidths]
        );
    }
}
```

## Credits
- [Asmit Nepal][link-asmit]

### Security

If you discover a security vulnerability within this package, please send an e-mail to asmitnepali99@gmail.com. All security vulnerabilities will be promptly addressed.

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### 📄 License
The MIT License (MIT). Please see [License File](LICENSE.txt) for more information.

[link-asmit]: https://github.com/AsmitNepali
