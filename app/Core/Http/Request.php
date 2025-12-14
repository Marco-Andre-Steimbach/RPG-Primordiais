<?php

namespace App\Core\Http;

class Request
{
    private array $body;
    private array $query;
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

    public function method(): string
    {
        return strtoupper($this->method);
    }

    public function uri(): string
    {
        return $this->uri;
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
