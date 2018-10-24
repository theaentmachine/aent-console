<?php

namespace TheAentMachine\Helper;

use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\StringsException;
use function Safe\mkdir;
use function Safe\sprintf;
use function Safe\glob;
use function Safe\unlink;
use function Safe\file_put_contents;
use function Safe\file_get_contents;

/**
 * Class in charge of assembling replies from the different containers.
 */
final class ReplyAggregator
{
    /**
     * @var string
     */
    private $replyDirectory;

    /**
     * ReplyAggregator constructor.
     * @param string|null $replyDirectory
     * @throws FilesystemException
     * @throws StringsException
     */
    public function __construct(string $replyDirectory = null)
    {
        if ($replyDirectory === null) {
            $replyDirectory = \sys_get_temp_dir().'/replies';
        }
        $this->replyDirectory = rtrim($replyDirectory, '/').'/';
        if (!\file_exists($replyDirectory)) {
            mkdir($replyDirectory, 0777, true);
            if (!is_dir($replyDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $replyDirectory));
            }
        }
    }

    /**
     * Purges all received replies
     * @throws FilesystemException
     */
    public function clear(): void
    {
        $files = glob($this->replyDirectory.'*'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file); // delete file
            }
        }
    }

    private function getNextFileName(): string
    {
        $i = 0;
        while (\file_exists($this->replyDirectory.'tmp'.$i)) {
            $i++;
        }
        return 'tmp'.$i;
    }

    /**
     * @param string $payload
     * @throws FilesystemException
     */
    public function storeReply(string $payload): void
    {
        $path = $this->replyDirectory.$this->getNextFileName();
        file_put_contents($path, $payload);
    }

    /**
     * @return string[]
     * @throws FilesystemException
     */
    public function getReplies(): array
    {
        $i = 0;
        $replies = [];
        while (\file_exists($this->replyDirectory.'tmp'.$i)) {
            $content = file_get_contents($this->replyDirectory.'tmp'.$i);
            if ($content === false) {
                throw new \RuntimeException('Failed to load file '.$this->replyDirectory.'tmp'.$i);
            }
            $replies[] = $content;
            $i++;
        }
        return $replies;
    }
}
