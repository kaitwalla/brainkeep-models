# BrainKeep Models

Shared Eloquent models for BrainKeep applications.

## Installation

```bash
composer require brainkeep/models
```

## Models

- `Note` - Content notes with support for multiple types (note, photo, quote, video, audio, link, question, book)
- `Tag` - Organizational tags for notes
- `Page` - Static pages
- `Image` - Image attachments for notes
- `NoteTag` - Pivot model for note-tag relationships

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=brainkeep-models-config
```

### User Model

By default, the package references `App\Models\User`. To use a different user model:

```php
// config/brainkeep-models.php
return [
    'user_model' => App\Models\User::class,
];
```

## Usage

```php
use Brainkeep\Models\Models\Note;
use Brainkeep\Models\Models\Tag;

// Or extend in your app's models:
namespace App\Models;

use Brainkeep\Models\Models\Note as BaseNote;

class Note extends BaseNote
{
    // Add app-specific functionality
}
```
