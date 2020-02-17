<?php

declare(strict_types=1);

namespace Messenger\Functional;

trait TemporaryFileTrait
{
    public static function createTemporaryFile(string $filename, string $content): void
    {
        \file_put_contents(self::TMP_PATH . $filename, $content);
    }

    protected function removeTemporaryFiles(): void
    {
        \array_map('unlink', \array_filter((array) \glob(self::TMP_PATH . '*')));
    }

    protected function getTemporaryFile(string $filename): string
    {
        return \file_get_contents(self::TMP_PATH . $filename);
    }
}
