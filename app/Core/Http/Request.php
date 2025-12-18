<?php

namespace App\Core\Http;

class Request
{
    private array $body;
    private array $query;
    private array $params = [];
    private string $method;
    private string $uri;

    private mixed $user = null;

    public function __construct()
    {
        $this->body = json_decode(file_get_contents("php://input"), true) ?? [];
        $this->query = $_GET ?? [];
        $this->method = $_SERVER["REQUEST_METHOD"] ?? "GET";
        $this->uri = strtok($_SERVER["REQUEST_URI"], '?') ?? "/";
    }

    public function body(): array
    {
        return $this->body;
    }

    public function query(): array
    {
        return $this->query;
    }

    public function params(): array
    {
        return $this->params;
    }

    public function param(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function method(): string
    {
        return strtoupper($this->method);
    }

    public function uri(): string
    {
        return rtrim($this->uri, '/') ?: '/';
    }

    public function setUser(mixed $user): void
    {
        $this->user = $user;
    }

    public function user(): mixed
    {
        return $this->user;
    }
}
