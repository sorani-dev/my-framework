<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Http;

use Intervention\Image\ImageManager;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Helper class to upload files to server
 */
class Upload
{
    /**
     * Path to the upload directory
     * @var string
     */
    protected $path;

    /**
     * File formats to transform into
     *
     * @var string[]
     */
    protected $formats = [];

    /**
     * Upload Constructor
     *
     * @param string|null $path Path to the directory to upload
     * @param array  $formats
     */
    public function __construct($path = null, array $formats = [])
    {
        if (null !== $path) {
            $this->path = $path;
        }
        if (is_array($formats)) {
            $this->formats += $formats;
        }
    }

    /**
     * Get a file as input to upload and returns the old file's name or null if an error occurred
     *
     * @param \Psr\Http\Message\UploadedFileInterface $file current file to upload
     * @param  string      $oldFile
     * @return string|null name of the old file
     */
    public function upload(UploadedFileInterface $file, ?string $oldFile = null): ?string
    {
        if ($file->getError() === UPLOAD_ERR_OK) {
            $this->delete($oldFile);
            $targetPath = $this->addCopySuffix($this->path . DIRECTORY_SEPARATOR . $file->getClientFilename());
            $dirname = pathinfo($targetPath, PATHINFO_DIRNAME);
            if (!file_exists($dirname)) {
                mkdir($dirname, 777, true);
            }
            $file->moveTo($targetPath);
            $this->generatePaths($targetPath);
            $dirname = pathinfo(
                $targetPath,
                PATHINFO_DIRNAME
            );
            return pathinfo($targetPath, PATHINFO_BASENAME);
        }
        return null;
    }

    /**
     * @return array get the current formats
     */
    public function getFormats(): array
    {
        return $this->formats;
    }

    /**
     * @param string[] $formats
     * @return Upload
     */
    public function setFormats(array $formats): self
    {
        $this->formats = $formats;
        return $this;
    }

    /**
     * Path of the upload directory
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set the directory path
     *
     * @param string $path
     * @return Upload
     */
    public function setPath($path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Add a suffix to the file to avoid ovewriting
     *
     * @param string $targetPath path to upload to
     * @return string
     */
    private function addCopySuffix($targetPath): string
    {
        while (file_exists($targetPath)) {
            $info = pathinfo($targetPath);
            $targetPath = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '_copy.' . $info['extension'];
        }
        return $targetPath;
    }

    /**
     * Delete a file
     *
     * @param string|null $file
     * @return void
     */
    public function delete(?string $file = null): void
    {
        if (null === $file) {
            return;
        }

        $file = $this->path . DIRECTORY_SEPARATOR . $file;
        if (file_exists($file)) {
            unlink($file);
        }
        foreach ($this->formats as $format => $size) {
            $oldFile = $this->getPathWithSuffix($file, $format);
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
    }

    /**
     * Get the suffix path if needed
     *
     * @param string $path input path
     * @param string $suffix suffix to add to path
     * @return string Suffixed path
     */
    private function getPathWithSuffix($path, $suffix): string
    {
        $info = pathinfo($path);
        $destination = $info['dirname'] .
            DIRECTORY_SEPARATOR .
            $info['filename'] . "_" . $suffix .
            "." . $info['extension'];
        return $destination;
    }
    /**
     * Generate paths depending on the formats
     *
     * @param string $targetPath path of the original file
     * @return void
     */
    private function generatePaths(string $targetPath): void
    {
        foreach ($this->formats as $format => $size) {
            $destination = $this->getPathWithSuffix($targetPath, $format);
            $manager = new ImageManager(['driver' => 'gd']);
            $manager->make($targetPath)->fit($size[0], $size[1])->save($destination);
        }
    }
}
