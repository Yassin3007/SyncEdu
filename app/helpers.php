<?php

use App\Models\ApiControle;

if (!function_exists("apiResponse")) {
    /**
     * @param string $message_key
     * @param array $items
     * @param int $code
     * @param int $http_code
     * @return \Illuminate\Http\JsonResponse
     */
    function apiResponse(string $message_key, array $items = [], int $code = 200, int $http_code = 200)
    {
        $return = [];
        $return["code"] = $code ;
        $return["message"] = trans($message_key);
        $return["items"] = $items;
        return response()->json($return, $http_code);
    }
}


if (!function_exists("fileInputFromUrl")) {
    /**
     * @param string $url
     * @return \Illuminate\Http\UploadedFile|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function fileInputFromUrl(string $url)
    {
        $tempFile = tempnam(sys_get_temp_dir(), md5(time()) . "_");
        $client = new \GuzzleHttp\Client();
        try {
            $client->get($url, [
                \GuzzleHttp\RequestOptions::SINK => $tempFile,
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return new \Illuminate\Http\UploadedFile($tempFile, basename($url));
    }
}

if (!function_exists('unicodeMessageEncode')) {
    function unicodeMessageEncode($message): string
    {
        return strtoupper(bin2hex(mb_convert_encoding($message, 'UCS-2', 'auto')));
    }
}


if (!function_exists('put_permanent_env')) {
    function put_permanent_env($key, $value)
    {
        if ($key != 'ARABIC_STATUS') {
            return false;
        }
        $path = app()->basePath() . DIRECTORY_SEPARATOR . '.env';

        $escaped = preg_quote('=' . env($key), '/');

        file_put_contents($path, preg_replace(
            "/^{$key}{$escaped}/m",
            "{$key}={$value}",
            file_get_contents($path)
        ));
        return true;
    }
}

if (!function_exists('apiIsOpen')) {
    function apiIsOpen($name, $portal = 'client')
    {
        $api = ApiControle::where('api_name', $name)->where('portal', $portal)->first();
        if ($api->status == true) {
            return true;
        }
        return false;
    }
}

if (!function_exists('isHuman')) {
    function isHuman($recaptchaToken = null, $deviceType = 'web'): bool
    {
//        return true; //temp success until frontend integration
        if ($deviceType == 'web') {
            $recaptcha = new \ReCaptcha\ReCaptcha(env('RECAPTCHA_SECRET','6Lc6rUgmAAAAAPw35L2zipKyQQoJKlSdHS7XOsEa'));
            $response = $recaptcha->verify($recaptchaToken);
            if (!$response->isSuccess())
                return false;
        }
        return true;
    }
}

