<?php

namespace App\Services;

use App\Models\ShortUrl as ShortUrlModel;
use Ramsey\Uuid\Uuid;

class ShortUrl
{
    protected static $checkUrlExists = false;

    public function shortCodeToUrl($code)
    {
        if (empty($code)) {
            throw new \App\Exceptions\BadRequest('Не получен код.');
        }

        $shortUrl = ShortUrlModel::where('short_code', '=', $code)->first();

        if (is_null($shortUrl)) {
            throw new \App\Exceptions\BadRequest('URL по такому коду не найден!');
        }

        return $shortUrl->long_url;
    }

    public function urlToShortCode($url)
    {
        if (empty($url)) {
            throw new \App\Exceptions\BadRequest("Не получен адрес URL.");
        }

        if ($this->validateUrlFormat($url) == false) {
            throw new \App\Exceptions\BadRequest(
                "Адрес URL имеет неправильный формат."
            );
        }

        if (self::$checkUrlExists) {
            if (!$this->verifyUrlExists($url)) {
                throw new \App\Exceptions\BadRequest(
                    "Адрес URL не существует."
                );
            }
        }

        $shortCode = $this->urlExistsInDb($url);

        if ($shortCode == false) {
            $shortCode = $this->createShortCode($url);
        }

        return $shortCode;
    }

    protected function validateUrlFormat($url)
    {
        return filter_var(
            $url,
            FILTER_VALIDATE_URL,
            // FILTER_FLAG_HOST_REQUIRED
        );
    }

    protected function verifyUrlExists($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return (!empty($response) && $response != 404);
    }

    protected function urlExistsInDb($url)
    {
        $result = ShortUrlModel::where('long_url', '=', $url)->first();

        return (is_null($result)) ? false : $result->short_code;
    }

    protected function createShortCode($url)
    {
        $shortCode = $this->generateUuid();

        ShortUrlModel::create([
            'long_url' => $url,
            'short_code' => $shortCode,
            'created_at' => \Carbon\Carbon::now(),
        ]);

        return $shortCode;
    }

    protected function generateUuid()
    {
        $code = Uuid::uuid1()->toString();
        $code = str_replace('-', '', $code);

        return substr($code, 0, (strlen($code) > 20) ? 20 : strlen($code));
    }
}
