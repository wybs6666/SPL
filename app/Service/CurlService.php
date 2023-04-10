<?php

namespace App\Service;

class CurlService
{
    /**
     * 发起 GET 请求，并返回数据
     *
     * @param string $url 请求 URL
     * @param array $params 请求参数
     * @param array $headers 请求头部信息
     * @param bool $isJsonResponse 是否是 JSON 格式的响应数据
     * @return mixed
     */
    public static function get($url, $params = [], $headers = [], $isJsonResponse = true)
    {
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        curl_close($ch);

        return $isJsonResponse ? json_decode($response, true) : $response;
    }

    /**
     * 发起 POST 请求，并返回数据
     *
     * @param string $url 请求 URL
     * @param mixed $data 请求数据
     * @param array $headers 请求头部信息
     * @param bool $isJsonRequest 是否是 JSON 格式的请求数据
     * @param bool $isJsonResponse 是否是 JSON 格式的响应数据
     * @return mixed
     */
    public static function post($url, $data = [], $headers = [], $isJsonRequest = true, $isJsonResponse = true)
    {
        $ch = curl_init();

        if ($isJsonRequest) {
            $data = json_encode($data);
            $headers = array_merge($headers, ['Content-Type: application/json']);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        curl_close($ch);

        return $isJsonResponse ? json_decode($response, true) : $response;
    }
}
