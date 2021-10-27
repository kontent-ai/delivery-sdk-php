<?php

/**
 * Retrieves language as corresponding language objects.
 */

namespace Kentico\Kontent\Delivery;

/**
 * Class LanguageFactory
 * @package Kentico\Kontent\Delivery
 */
class LanguageFactory
{
    /**
     * LanguageFactory constructor.
     */
    public function __construct()
    {
    }

    /**
     * Crafts an array of languages
     *
     * Parses response for languages and returns array of
     * _Language_ objects that represent them.
     *
     * @param $response object response body for languages request.
     * @return Language[] array of Language objects.
     */
    public function createLanguages($response)
    {
        $languages = array();
        if ($this->isInvalidResponse($response)) {
            return $languages;
        }

        $languagesData = get_object_vars($response)["languages"];
        foreach ($languagesData as $language) {
            $languages[] =  $this->prepareLanguage($language);
        }

        return $languages;
    }


    /**
     * Crafts Language object.
     *
     * @param $languageItem object representing single language.
     * @return Language object
     */
    private function prepareLanguage($languageItem)
    {
        // Acquire data for 'system' property
        $system = new Models\Languages\LanguageSystem(
            $languageItem->system->id,
            $languageItem->system->name,
            $languageItem->system->codename
        );

        $newLanguage = new Models\Languages\Language();
        $newLanguage->system = $system;

        return $newLanguage;
    }

    /**
     * Checks whether response parameter is invalid.
     *
     * @param $response object response body for languages.
     * @return bool True on invalid response body, false on valid.
     */
    private function isInvalidResponse($response)
    {
        if (empty($response) || is_null($response)) {
            return true;
        }

        $notLanguagesFormat = !isset(get_object_vars($response)["languages"]);

        return $notLanguagesFormat;
    }
}
