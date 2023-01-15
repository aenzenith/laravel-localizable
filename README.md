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

### Adding the `Localizable` trait and `$localizable` property to your model

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

You can use the **getLocalizables** method to get the localizable fields of the model for each locale to pass them to the front-end. For example:

```php
    $localizables = Content::getLocalizables();

    //you can pass also locales list with config('localizable.locales') for language names

    return view('content.create', compact('localizables'));
```

The `localizables` variable will be added to your view data:

```json
{
  "localizables": {
    "en": {
      "title": null,
      "content": null
    },
    "fr": {
      "title": null,
      "content": null
    },
    "es": {
      "title": null,
      "content": null
    },
    "de": {
      "title": null,
      "content": null
    }
  }
}
```

You can create a form with the fields you added to the `$localizable` array and pass the `localizables` variable to the form. Then you can use the `localizables` variable to create the localized fields in the form.

Here is an example of a form that uses the `localizables` variable:

```html

    <form action="{{ route('content.store') }}" method="POST">
        @csrf

        @foreach ($localizables as $locale => $fields)
            <div>
                <label>({{ $locale }}) Title</label>
                <input type="text" name="translations[{{ $locale }}][title]">
            </div>
            <div>
                <label>({{ $locale }}) Content</label>
                <textarea name="translations[{{ $locale }}][content]"></textarea>
            </div>
        @endforeach

        <button type="submit">Submit</button>
    </form>
```

In the Vue.js with Inertia.js:

```php
    return Inertia::render('Content/Create', [
        'localizables' => Content::getLocalizables(),
    ]);
```

```javascript

    import { useForm } from '@inertiajs/inertia';
    import { defineProps } from 'vue';

    const props = defineProps({
        localizables: {
            type: Object,
            required: true,
        },
    });

    const form = useForm({
        translations: props.localizables,
    });

    const submit = () => {
        form.post(route('content.store'));
    };
```

```html
    <form>

        <div v-for="(fields, locale) in localizables" :key="locale">
            <div>
                <label>({{ $locale }}) Title</label>
                <input type="text" v-model="form.translations[locale].title">
            </div>
            <div>
                <label>({{ $locale }}) Content</label>
                <textarea v-model="form.translations[locale].content"></textarea>
            </div>
        </div>

        <button type="button" @click="submit()">Submit</button>

    </form>
```

When saving a model in a controller, you can use the following localization methods to handle the localization data:

1. The **translate** method allows you to localize a specific field of the model to a specific locale. It accepts three arguments: `locale`, `field` and `value`. For example:

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
    $content = Content::first()->getTranslations();
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
    },
    "es": {
      "title": null,
      "content": null
    },
    "de": {
      "title": null,
      "content": null
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

All of the localized data will stored in `localizations` table. When a model is deleted, the related localizations will be deleted automatically.

## License

[MIT](https://choosealicense.com/licenses/mit/)

## Support

If you have any questions or suggestions, please feel free to contact me. You can also open an issue on GitHub.

[!["Buy Me A Coffee"](https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png)](https://www.buymeacoffee.com/aenzenith)
