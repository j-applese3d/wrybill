<?php

declare(strict_types=1);

namespace Wrybill;

class Wrybill
{
    // BE VERY WARY OF WHAT YOU ENTER HERE
    public const PIPER_COMMAND = '../piper/piper --model ../piper/en_US-amy-medium.onnx';

    public function __construct(
        public string $audioFilesDirectory,
        public string $cacheDirectory,
        public string $piperCmd,
        public bool $mkCache = true,
    )
    {
        $this->buildCaches();
    }

    private function buildCaches(): void
    {
        if ($this->mkCache && !is_dir($this->cacheDirectory)) {
            if (!mkdir($this->cacheDirectory) && !is_dir($this->cacheDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->cacheDirectory));
            }
        }

        // read all the XC files, and generate the bird name .wav s
        foreach ($this->getAudioFiles() as $file) {

            // generate piper wav file.
            $englishNameClean = escapeshellarg($file['species_en']);
            $outFile = $this->cacheDirectory . '/' . $file['species_en'] . '.wav';
            if (file_exists($outFile)) {
                continue;
            }

            $outFileNameClean = escapeshellarg($outFile);
            shell_exec("echo $englishNameClean | " . self::PIPER_COMMAND . " --output_file $outFileNameClean");
        }
    }

    public function getAudioFiles(): array
    {
        // find all the files, grouped by folder, whose filenames start with XC
        // In bash I'd do: find $audioFilesDirectory -type f -name "XC*"

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->audioFilesDirectory),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        $audioFiles = [];

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isFile() && str_starts_with($file->getFilename(), 'XC')) {
                $path = $file->getPathname();
                $filename = $file->getFilename();
                $filenamePcs = explode(' - ', $filename);

                $audioFiles[] = [
                    'path' => $path,
                    'filename' => $filename,
                    'xeno_id' => $filenamePcs[0],
                    'species_en' => $filenamePcs[1],
                    'species_name_audio_file' => $this->cacheDirectory . '/' . $filenamePcs[1] . '.wav',
                    'species_scientific_name' => $filenamePcs[2],
                ];
            }
        }

        usort($audioFiles, fn ($a, $b) => $a['species_en'] <=> $b['species_en']);

        return $audioFiles;
    }
}
