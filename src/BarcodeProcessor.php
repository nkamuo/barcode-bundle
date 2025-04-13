<?php
namespace Nkamuo\Barcode;

use Nkamuo\Barcode\Decoder\BarcodeDecoderInterface;
use Nkamuo\Barcode\Encoder\BarcodeEncoderInterface;
use Nkamuo\Barcode\Exception\BarcodeDecodeException;
use Nkamuo\Barcode\Exception\BarcodeEncodeException;
use Nkamuo\Barcode\Exception\BarcodeGenerationException;
use Nkamuo\Barcode\Factory\BarcodeFactoryInterface;
use Nkamuo\Barcode\Generator\BarcodeGeneratorInterface;
use Nkamuo\Barcode\Model\BarcodeInterface;
use Nkamuo\Barcode\Repository\BarcodeRepositoryInterface;


class BarcodeProcessor  implements BarcodeProcessorInterface{

    
    public function __construct(
        private readonly BarcodeFactoryInterface $factory,
        private readonly BarcodeEncoderInterface $encoder,
        private readonly BarcodeDecoderInterface $decoder,
        private readonly BarcodeGeneratorInterface $generator,
        private readonly BarcodeRepositoryInterface $repository,
    ){

    }

    /**
     * @inheritDoc
     * @throws BarcodeDecodeException
     */
    public function decode( string $data, string|null $symbol = null, string|null $format = null, array $context = []): BarcodeInterface {
        $barcode = $this->factory->createWritable($context);
        $this->decoder->decode($barcode, $data, $symbol, $format, $context);
        return $barcode;
    }
    
    /**
     * @inheritDoc
     * @throws BarcodeEncodeException
     */
    public function encode(BarcodeInterface $barcode, string $symbol, string|null $format = null, array $context = []): string {
        if(!$this->encoder->supports($barcode, $symbol, $format, $context)){
            throw new \InvalidArgumentException("Unsupported barcode or symbol");
        }
        
        return $this->encoder->encode($barcode, $symbol, $format, $context);
    }
    
    /**
     * @inheritDoc
     * @throws BarcodeGenerationException
     */
    public function generate(array $context = []): BarcodeInterface {
        if(!$this->generator->supports($context)){
            throw BarcodeGenerationException::disabled();
        }

        $barcode = $this->factory->createWritable($context);
        $this->generator->generate($barcode, $context);
        return $barcode;
    }

    /**
     * @inheritDoc
     * @throws BarcodeDecodeException
     */
    public function search(string $data, array $context = []): array
    {
        $barcode = $this->decode($data, null, null, $context);
        return $this->repository->search($barcode, $context);
    }
}