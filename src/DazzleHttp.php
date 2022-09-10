<?php

class DazzleHttp
{
    protected array $config = [];
    protected array $headers = [];
    protected string $baseUrl = '';
    protected CurlHandle $handler;

    public function __construct(
        string $baseUrl,
        array $config = [],
        array $headers = [],
    ) {
        $this->handler = \curl_init();
        $this->baseUrl = $baseUrl;
        $this->config = $config;
        $this->headers = $headers;
    }

    public function url(string $endpoint = ''): string
    {
        return \rtrim($this->baseUrl, '/') . '/' . \ltrim($endpoint, '/');
    }

    public function config(array $config = [])
    {
        return \array_merge($this->config, $config);
    }

    public function headers(array $headers = [])
    {
        return \array_merge($this->headers, $headers);
    }

    public function release(bool $reinit = false): void
    {
        \curl_close($this->handler);
        if ($reinit) {
            $this->handler = \curl_init();
        }
    }

    public function info(): array
    {
        return \curl_getinfo($this->handler);
    }

    public function request(
        string $method = 'get',
        string $endpoint = '',
        array $headers = [],
        array $payload = [],
        array $config = []
    ): array {
        \curl_setopt($this->handler, CURLOPT_URL, $this->url($endpoint));
        \curl_setopt($this->handler, CURLOPT_HEADER, $this->headers($headers));
        \curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, true);
        if (strtolower($method) !== 'get') {
            \curl_setopt($this->handler, CURLOPT_CUSTOMREQUEST, strtoupper($method));
            \curl_setopt($this->handler, CURLOPT_POSTFIELDS, http_build_query($payload));
        }
        foreach ($this->config($config) as $opt => $val) {
            \curl_setopt($this->handler, $opt, $val);
        }
        $result = \curl_exec($this->handler);
        \curl_reset($this->handler);
        return \compact('result');
    }
}
