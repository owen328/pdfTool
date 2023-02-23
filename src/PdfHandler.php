<?php

namespace owen328\Pdf2Img;

class PdfHandler
{
    /**
     * @var \Imagick
     */
    private $imagick;
    /**
     * @var string
     */
    private $pdfFile;
    /**
     * @var int
     */
    private $numberOfPages;
    
    private string $format;
    
    private int $resolution;
    

    public function __construct(string $pdfFile)
    {
        if (!file_exists($pdfFile)) {
            throw new \Exception("File `{$pdfFile}` does not exist");
        }
        $this->imagick = new \Imagick();
        $this->imagick->pingImage($pdfFile);
        $this->numberOfPages = $this->imagick->getNumberImages();
        $this->pdfFile = $pdfFile;

        $this->imagick->clear();
    }
    
    public function setResolution(int $resolution): void
    {
        $this->resolution = $resolution;
    }
    
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function saveSingleImage($imagePath): void
    {
        $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
        $this->imagick->setResolution($this->resolution, $this->resolution);
        $this->imagick->readImage($this->pdfFile);
        $this->imagick->resetIterator();
        for ($i = 0; $i < $this->numberOfPages; $i++) {
            $this->imagick->setIteratorIndex($i);
            $this->imagick->trimImage(0);
        }

        $this->imagick->resetIterator();

        $combine = $this->imagick->appendImages(true);

        $combine->setFormat($ext);
        $combine->setCompressionQuality(95);
        $combine->writeImage($imagePath);
    }
    
    
    public function saveAllPagesAsImages(string $directory)
    {
        $numberOfPages = $this->numberOfPages;
        $this->imagick->setResolution(150, 150);
        $this->imagick->readImage($this->pdfFile);
        $this->imagick->setFormat($this->format);
        $this->imagick->setCompressionQuality(95);
        $this->imagick->writeImages($directory . DIRECTORY_SEPARATOR . pathinfo($this->pdfFile, PATHINFO_FILENAME) . '.' . $this->format, false);
    }


}