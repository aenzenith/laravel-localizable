# Laravel Localizable

This trait offers a convenient way to handle the localization of model fields within a Laravel application. It provides functionality for setting and translating localizable fields of a model, as well as methods for deleting and retrieving translations. The trait utilizes the package's configuration file to establish default values and fallback options for localization, and also enables the retrieval of translations for localizable fields of a model by locale or field. Overall, it streamlines the localization process for your Laravel models.

## Installation

To install the package, you can use the following commands:

```bash
composer require aenzenith/laravel-localizable

```

After installation completed, you have to run these commands to prepare package ready to use:

```bash
php artisan migrate

php artisan vendor:publish --tag=config --force
```

With publishing config you can access the config file from `config/localizable.php`

## Usage

### Setting the locales list

You can modify the default locales in the configuration file by adding new languages in the form of language codes and language names to the locales array. For example, if you want to add Spanish and German to the list of available languages, you can add the following lines to your configuration file:

```php
'locales' => [
    'en' => 'English',
    'fr' => 'French',
    'es' => 'Spanish',
    'de' => 'German',
],
```

### 1. Adding the `Localizable` trait to model

```php
use Aenzenith\LaravelLocalizable\Localizable;

class Content extends Model
{
    use HasFactory, Localizable;

    protected $localizable = [
        'title',
        'content',
    ];

    /* ... */
}

```

The fields you added to the `$localizable` array don't have to be in the database table. You can add a localizable attribute without being dependent on any database field.

However, if you want to localize the existing fields in your model,
the fields you added to the `$localizable` array will be returned as
translated when the model is called.

The localizations will be applied automaticly, according to application locale. There is not needed another action to retrieving localized data.

#### If there is no record for the field you added to `$localizable`, the `field_fallback_value` in `config/localizable.php` will be returned. If you want it to return as null, set the `field_fallback` option to `false`.

```php
    'field_fallback' => true,

    'field_fallback_value' => 'This field is not translated yet.',
```

### 2. Localization process

When saving a model in a controller, you can use the following localization methods to handle the localization data:

1. **translate** : This method allows you to localize a field of the model to a specific locale. It accepts three arguments: `locale`, `field`, `value`

```php
    $content = new Content();
    $content->save();

    $content->translate('en', 'title', 'English Title');
    $content->translate('en', 'content', 'English Content');

    $content->translate('fr', 'title', 'French Title');
    $content->translate('fr', 'content', 'French Content');
```

2. **translateMany** : This method allows you to localize multiple fields of the model to a specific locale. It accepts two arguments: `locale`, `fields` where fields is an associative array of field and value.

```php
    $content->translateMany(
        'en',
        [
            'title' => 'English Title',
            'content' => 'English Content'
        ]
    );

    $content->translateMany(
        'fr',
        [
            'title' => 'French Title',
            'content' => 'French Content'
        ]
    );
```

3. **translateManyLocales** : This method allows you to localize the model to multiple locales. It accepts array where keys are locales and values are arrays of fields and their values.

```php
    $content->translateManyLocales(
        [
            'en' => [
                'title' => 'English Title',
                'content' => 'English Content'
            ],
            'fr' => [
                'title' => 'French Title',
                'content' => 'French Content'
            ]
        ]
    );
```

### 3. Retrieving localizations for updating

You can get the translated datas with using **getTranslations** method after you called the model.

```php
    $content = Content::first();
    $content->getTranslations()
```

The `translations` attribute will be added to your model data:

```json
{
  "id": 1,
  "created_at": "2023-01-14T19:18:55.000000Z",
  "updated_at": "2023-01-14T19:18:55.000000Z",
  "title": "Englist Title",
  "content": "English Content",
  "translations": {
    "en": {
      "title": "Englist Title",
      "content": "English Content"
    },
    "fr": {
      "title": "French Title",
      "content": "French Content"
    },
    "ru": {
      "title": null,
      "content": null
    }
  }
}
```

If you want to group translations by fields instead of by locale, you can pass the `field` option as a parameter to the getTranslations method. This will return an array of translations grouped by the field name.

```php
    $content->getTranslations('field')
```

Then you can process the already localized values in front-end and update with **translate**, **translateMany** and **translateManyLocales** methods.
