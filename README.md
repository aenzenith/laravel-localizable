# Laravel Localizable

This trait offers an efficient and user-friendly solution for localizing model fields within a Laravel application. It grants the ability to set localizable fields without adding new database fields and the capability to translate existing table fields to different languages without adding new fields. This trait streamlines the localization process for your Laravel models by simplifying the management and maintenance of localizable fields.

## Installation

To install the package, you can use the following commands:

```bash
composer require aenzenith/laravel-localizable

```

After installation completed, you have to run these commands to prepare package ready to use:

```bash
php artisan migrate

php artisan vendor:publish --provider="Aenzenith\LaravelLocalizable\LocalizableServiceProvider"
```

With publishing, you can access the config file from `config/localizable.php`

## Usage

### Setting the locales list

You can modify the locales that will be use, in the `config/localizable.php` configuration file by adding new languages in the form of language codes and language names to the locales array. For example, if you want to add Spanish and German to the list of available languages, simply you can add the following lines to your configuration file:

```php
'locales' => [
    'en' => 'English',
    'fr' => 'French',
    /* */
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

The fields added to the `$localizable` array do not need to have a corresponding field in the database table. This allows you to add localizable attributes independently of the database, giving you the flexibility to handle localization without making changes to the underlying table structure. 

However, if you wish to localize existing fields in your model, the fields added to the `$localizable` array will be returned as localized when the model is retrieved, without the need for any changes to the table structure.

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
  @csrf @foreach ($localizables as $locale => $fields)
  <div>
    <label>({{ $locale }}) Title</label>
    <input type="text" name="localizations[{{ $locale }}][title]" />
  </div>
  <div>
    <label>({{ $locale }}) Content</label>
    <textarea name="localizations[{{ $locale }}][content]"></textarea>
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
import { useForm } from "@inertiajs/inertia";
import { defineProps } from "vue";

const props = defineProps({
  localizables: {
    type: Object,
    required: true,
  },
});

const form = useForm({
  localizations: props.localizables,
});

const submit = () => {
  form.post(route("content.store"));
};
```

```html
<template>
  <form>
    <div v-for="(fields, locale) in localizables" :key="locale">
      <div>
        <label>({{ $locale }}) Title</label>
        <input type="text" v-model="form.localizations[locale].title" />
      </div>
      <div>
        <label>({{ $locale }}) Content</label>
        <textarea v-model="form.localizations[locale].content"></textarea>
      </div>
    </div>

    <button type="button" @click="submit()">Submit</button>
  </form>
</template>
```

While utilizing the `localizables` variable to create localized fields in the form is an option, it is not a requirement. You have the freedom to use your preferred front-end framework to design these fields in a way that aligns with your project's specific needs and requirements. You only need to ensure that you pass the correct parameters to the methods used when saving to the back-end. Below you can find information about the controller methods.

When saving a model in a controller, you can use the following localization methods to handle the localization data:

1. The **localize** method allows you to localize a specific field of the model to a specific locale. It accepts three arguments: `locale`, `field` and `value`. For example:

```php
    $content = new Content();
    $content->save();

    $content->localize('en', 'title', 'English Title');
    $content->localize('en', 'content', 'English Content');

    $content->localize('fr', 'title', 'French Title');
    $content->localize('fr', 'content', 'French Content');
```

2. The **localizeMany** method allows you to localize multiple fields of the model to a specific locale. It accepts two arguments: the `locale` and `an associative array of fields and their values`. For example:

```php
    $content->localizeMany(
        'en',
        [
            'title' => 'English Title',
            'content' => 'English Content'
        ]
    );

    $content->localizeMany(
        'fr',
        [
            'title' => 'French Title',
            'content' => 'French Content'
        ]
    );
```

3. The **localizeManyLocales** method allows you to localize the model to multiple locales. It accepts an array where the keys are the locales and the values are arrays of fields and their values. For example:

```php
    $content->localizeManyLocales(
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

If you got the localizables with **getLocalizables** method and processed in the front-end, you can use the **localizeManyLocales** method to create the localized values easily. For example:

```php
    $content->localizeManyLocales($request->localizations);
```

### Retrieving localizations for update

You can get the localized datas with using **getLocalizations** method after you called the model.

```php
    $content = Content::first()->getLocalizations();
    /* or */
    $content = Content::first();
    $content->getLocalizations();
```

The `localizations` attribute will be added to your model data:

```json
{
  "localizations": {
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

Then you can process the localized values in front-end and pass the updated values to **localizeManyLocales** method easily. For example:

```php
    $content->localizeManyLocales($request->localizations);
```

### Getting localized data

The localized data will be returned automatically according to the application locale. To change the application locale, you can use the `setLocale` method.

```php
    app()->setLocale('fr');
```

#### If there is no record for the field you added to `$localizable`, the `field_fallback_value` in `config/localizable.php` will be returned. If you want it to return as null, set the `field_fallback` option to `false`.

```php
    'field_fallback' => true,

    'field_fallback_value' => 'This field is not localized yet.',
```

All of the localized data will stored in `localizations` table. When a model is deleted, the related localizations will be deleted automatically.

## License

[MIT](https://github.com/aenzenith/laravel-localizable/blob/main/LICENSE)

## Support

If you have any questions or suggestions, please feel free to contact me. You can also open an issue on GitHub.

[!["Buy Me A Coffee"](https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png)](https://www.buymeacoffee.com/aenzenith)
