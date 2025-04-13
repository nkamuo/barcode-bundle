<?php
namespace Nkamuo\Barcode\Exception;


class BarcodeGenerationException extends \Exception{

    public static function notSupported(array $config = []): self
    {
        return new static("Not supported config: ".implode(", ", $config));
    }

    public static function notFound(): self
    {
        return new static("Not found barcode generator");
    }

    public static function disabled(): self
    {
        return new static("Barcode generator disabled");
    }
}