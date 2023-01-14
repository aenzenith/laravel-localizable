# Laravel Localizable

This trait offers a convenient way to handle the localization of model fields within a Laravel application. It provides functionality for setting and translating localizable fields of a model. The trait utilizes the package's configuration file to establish default values and fallback options for localization, and also enables the retrieval of translations for localizable fields of a model by locale or field. Overall, it streamlines the localization process for your Laravel models.

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

### Adding the `Localizable` trait to model

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

### Localization process

When saving a model in a controller, you can use the following localization methods to handle the localization data:

1. The **translate** method allows you to localize a specific field of the model to a specific locale. It accepts three arguments: It accepts three arguments: `locale`, `field` and `value`. For example:

```php
    $content = new Content();
    $content->save();

    $content->translate('en', 'title', 'English Title');
    $content->translate('en', 'content', 'English Content');

    $content->translate('fr', 'title', 'French Title');
    $content->translate('fr', 'content', 'French Content');
```

2. The **translateMany** method allows you to localize multiple fields of the model to a specific locale. It accepts two arguments: the `locale` and `an associative array of fields and their values`. For example:

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

3. The **translateManyLocales** method allows you to localize the model to multiple locales. It accepts an array where the keys are the locales and the values are arrays of fields and their values. For example:

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

### Retrieving localizations for updating

You can get the localized datas with using **getTranslations** method after you called the model.

```php
    $content = Content::first()>getTranslations();
    /* or */
    $content = Content::first();
    $content->getTranslations();
```

The `translations` attribute will be added to your model data:

```json
{
  "translations": {
    "en": {
      "title": "Englist Title",
      "content": "English Content"
    },
    "fr": {
      "title": "French Title",
      "content": "French Content"
    }
  }
}
```

Then you can process the localized values in front-end and pass the updated values to **translateManyLocales** method easily. For example:

```php
    $content->translateManyLocales($request->translations);
```

### Getting localized data

The localized data will be returned automatically according to the application locale. To change the application locale, you can use the `setLocale` method.

```php
    app()->setLocale('fr');
```

#### If there is no record for the field you added to `$localizable`, the `field_fallback_value` in `config/localizable.php` will be returned. If you want it to return as null, set the `field_fallback` option to `false`.

```php
    'field_fallback' => true,

    'field_fallback_value' => 'This field is not translated yet.',
```

## License

[MIT](https://choosealicense.com/licenses/mit/)

## Support

If you have any questions or suggestions, please feel free to contact me. You can also open an issue on GitHub.

[!["Buy Me A Coffee"](https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png)](https://www.buymeacoffee.com/aenzenith)
