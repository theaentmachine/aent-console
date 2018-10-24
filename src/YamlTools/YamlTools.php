<?php

namespace TheAentMachine\YamlTools;

use Safe\Exceptions\FilesystemException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
use TheAentMachine\Yaml\Dumper;
use function Safe\chown;
use function Safe\chgrp;

final class YamlTools
{
    /**
     * Merge the content of $sourceFile into $destinationFile's one (overwritten)
     * @param string $destinationFile
     * @param string $sourceFile
     */
    public static function mergeTwoFiles(string $destinationFile, string $sourceFile): void
    {
        $files = [$destinationFile, $sourceFile];
        self::mergeSuccessive($files, $destinationFile);
    }

    /**
     * Given an array of yaml file pathnames, merge them from the last to the first
     * @param mixed[] $yamlFilePathnames
     * @param null|string $outputFile if null, dump the result to stdout
     */
    public static function mergeSuccessive(array $yamlFilePathnames, ?string $outputFile = null): void
    {
        $command = array('yaml-tools', 'merge', '-i');
        $command = array_merge($command, $yamlFilePathnames);
        if (null !== $outputFile) {
            $command[] = '-o';
            $command[] = $outputFile;
        }
        $process = new Process($command);
        $process->enableOutput();
        $process->setTty(true);
        $process->mustRun();
    }

    /**
     * Merge yaml content into one file (created if non existent, with its directory's owner and group code)
     * @param string|mixed[] $content
     * @param string $file
     * @throws FilesystemException
     */
    public static function mergeContentIntoFile($content, string $file): void
    {
        if (\is_array($content)) {
            $content = self::dump($content);
        }

        $fileSystem = new Filesystem();

        if ($fileSystem->exists($file)) {
            $tmpFile = $fileSystem->tempnam(sys_get_temp_dir(), 'yaml-tools-merge-');
            $fileSystem->dumpFile($tmpFile, $content);
            self::mergeTwoFiles($file, $tmpFile);
            $fileSystem->remove($tmpFile);
        } else {
            $fileSystem->dumpFile($file, $content);
            $dirInfo = new \SplFileInfo(\dirname($file));
            chown($file, $dirInfo->getOwner());
            chgrp($file, $dirInfo->getGroup());
        }
    }

    /**
     * Delete one yaml item given its path (e.g. key1 key2 0 key3) in the $inputFile, then write it into $outputFile (or stdout if empty)
     * Caution : this also deletes its preceding comments
     * @param string[] $pathToItem e.g. key1 key2 0 key3
     * @param string $file
     */
    public static function deleteYamlItem(array $pathToItem, string $file): void
    {
        $command = array('yaml-tools', 'delete');
        $command = array_merge($command, $pathToItem, [
            '-i', $file,
            '-o', $file,
        ]);
        $process = new Process($command);
        $process->enableOutput();
        $process->setTty(true);
        $process->mustRun();
    }

    /**
     * See https://github.com/thecodingmachine/yaml-tools#normalize-docker-compose
     * @param string $inputFile
     * @param string|null $outputFile
     */
    public static function normalizeDockerCompose(string $inputFile, ?string $outputFile = null): void
    {
        $command = array('yaml-tools', 'normalize-docker-compose', '-i', $inputFile);
        if (null !== $outputFile) {
            $command[] = '-o';
            $command[] = $outputFile;
        }
        $process = new Process($command);
        $process->enableOutput();
        $process->setTty(true);
        $process->mustRun();
    }

    /**
     * Dumps $item in YAML.
     *
     * @param mixed $item
     * @return string
     */
    public static function dump($item): string
    {
        $yaml = new Dumper(2);
        return $yaml->dump($item, 256, 0, Yaml::DUMP_OBJECT_AS_MAP);
    }
}
