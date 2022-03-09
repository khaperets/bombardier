<?php declare(strict_types=1);

final class Target
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getName(): string
    {
        $host = \parse_url($this->data['url'], \PHP_URL_HOST);
        $path = \parse_url($this->data['url'], \PHP_URL_PATH);

        return \rtrim(\preg_replace('#[^\w]#', '-', \mb_strtolower($host . $path)), '-');
    }

    public function getUrl(): string
    {
        return $this->data['url'];
    }

    public function getMethod(): ?string
    {
        if (false === isset($this->data['method'])) {
            return null;
        }

        return '--method=' . $this->data['method'];
    }

    public function getBody(): ?string
    {
        if (false === isset($this->data['body'])) {
            return null;
        }

        return \sprintf('--body="%s"', \http_build_query($this->data['body']));
    }

    public function getHeaders(): ?string
    {
        if (false === isset($this->data['headers'])) {
            return null;
        }

        $headers = [];

        foreach ($this->data['headers'] as $key => $value) {
            $headers[] = \sprintf('--header="%s: %s"', $key, $value);
        }

        return \implode(' ', $headers);
    }

    public function getReplicas(): int
    {
        return (int) ($this->data['replicas'] ?? getopt('', ['replicas::'])['replicas'] ?? 25);
    }

    public function getDuration(): string
    {
        return '--duration=' . ($this->data['duration'] ?? '72h');
    }

    public function getConnections(): string
    {
        return '--connections=' . ($this->data['connections'] ?? 1000);
    }

    public function createConfiguration(string $template): string
    {
        return \strtr($template, [
            '{{NAME}}' => $this->getName(),
            '{{URL}}' => $this->getUrl(),
            '{{METHOD}}' => $this->getMethod(),
            '{{BODY}}' => $this->getBody(),
            '{{HEADERS}}' => $this->getHeaders(),
            '{{REPLICAS}}' => $this->getReplicas(),
            '{{DURATION}}' => $this->getDuration(),
            '{{CONNECTIONS}}' => $this->getConnections(),
        ]);
    }
}

$targets = \json_decode(\file_get_contents(__DIR__ . '/targets.json'), true);
$template = \file_get_contents(__DIR__ . '/template.yaml');

foreach ($targets as $data) {
    $target = new Target($data);
    $configuration = $target->createConfiguration($template);

    \file_put_contents(__DIR__ . '/targets/' . $target->getName() . '.yaml', $configuration);
}
