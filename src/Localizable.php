<?php

namespace Aenzenith\LaravelLocalizable;

use Illuminate\Database\Eloquent\Model;
use Aenzenith\LaravelLocalizable\Models\Localization;

trait Localizable
{
    public static function booted()
    {
        static::retrieved(function (Model $model) {
            $model->localize();
        });

        static::deleted(function (Model $model) {
            $model->unlocalize();
        });
    }

    private function getConfig($key)
    {
        $default_conf = require __DIR__ . '/../config/localizable.php';

        $config = config('localizable.' . $key, $default_conf[$key]);

        return $config;
    }

    /**
     * This method is used to set the localizable fields of a model
     *
     * @param string $locale
     * @param string $field
     * @param string $value
     * @return Model
     */
    public function translate($locale, $field, $value = null)
    {
        if (!in_array($field, $this->localizable)) {
            throw new \Exception('Field "' . $field . '" is not localizable');
        }

        $localization = Localization::firstOrNew([
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'locale' => $locale,
            'field' => $field,
        ]);

        $localization->value = $value;
        $localization->save();

        return $this;
    }

    /**
     * This method is used to translate a field of a model, multiple locales and fields can be translated
     *
     * @param string $locale
     * @param array $fields [field => value]
     * @return Model
     */
    public function translateMany($locale, $fields)
    {
        foreach ($fields as $field => $value) {
            $this->translate($locale, $field, $value);
        }

        return $this;
    }

    /**
     * This method is used to translate a field of a model, multiple locales and fields can be translated
     *
     * @param array $localization_data [locale => [field => value]]
     * @return Model
     */
    public function translateManyLocales($localization_data)
    {
        foreach ($localization_data as $locale => $field) {
            $this->translateMany($locale, $field);
        }

        return $this;
    }

    private function unlocalize()
    {
        Localization::where([
            'model_type' => get_class($this),
            'model_id' => $this->id,
        ])->delete();

        return $this;
    }

    private function localize()
    {
        $localizables = $this->localizable ?? [];

        $current_locale = app()->getLocale();

        foreach ($localizables as $localizable) {
            $localization = Localization::where([
                'model_type' => get_class($this),
                'model_id' => $this->id,
                'locale' => $current_locale,
                'field' => $localizable,
            ])->first();

            $this->attributes[$localizable] = isset($localization->value) ?
                $localization->value : (isset($this->attributes[$localizable]) ? $this->attributes[$localizable] : ($this->getConfig('field_fallback') ?
                    $this->getConfig('field_fallback_value') :
                    null));
        }

        return $this;
    }

    /**
     * This method is used to get the translations of localizable fields of a model
     *
     * @return array
     */
    public function getTranslations()
    {
        $localizables = $this->localizable ?? [];

        $locales = $this->getConfig('locales');

        $translations = [];

        $query = function ($locale, $localizable) {
            return Localization::where([
                'model_type' => get_class($this),
                'model_id' => $this->id,
                'locale' => $locale,
                'field' => $localizable,
            ])->first();
        };

        foreach (array_keys($locales) as $locale) {
            $translations[$locale] = [];
            foreach ($localizables as $localizable) {
                $localization = $query($locale, $localizable);
                $translations[$locale][$localizable] = $localization->value ?? null;
            }
        }

        $this->attributes['translations'] = $translations;

        return $this;
    }
}
