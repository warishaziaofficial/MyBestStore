<?php

namespace Scripts\Lib;

class ImportLogger
{
    /** @var array<string, list<string>> */
    private array $buckets = [
        'created' => [],
        'updated' => [],
        'skipped' => [],
        'failed' => [],
    ];

    public function log(string $bucket, string $message): void
    {
        if (! isset($this->buckets[$bucket])) {
            $bucket = 'failed';
        }

        $this->buckets[$bucket][] = $message;
        $prefix = strtoupper($bucket);

        if ($bucket === 'failed') {
            fwrite(STDERR, "[{$prefix}] {$message}\n");
        } else {
            echo "[{$prefix}] {$message}\n";
        }
    }

    /** @return array<string, int> */
    public function counts(): array
    {
        return array_map('count', $this->buckets);
    }

    public function summary(string $title = 'Import summary'): void
    {
        $counts = $this->counts();
        echo "\n=== {$title} ===\n";
        echo "Created: {$counts['created']}\n";
        echo "Updated: {$counts['updated']}\n";
        echo "Skipped: {$counts['skipped']}\n";
        echo "Failed:  {$counts['failed']}\n";
    }
}
