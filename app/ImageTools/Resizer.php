<?php

namespace App\ImageTools;

class Resizer
{
    /**
     * Based upon the following Stack Overflow answer:
     * @link https://stackoverflow.com/a/16774644/5745438
     *
     * @param string $srcImageContent
     * @param int $maxDimension
     * @return string
     */
    public function resize(string $srcImageContent, int $maxDimension): string
    {
        // Create an image resource from the contents string.
        $srcImageResource = imagecreatefromstring($srcImageContent);

        // Calculate the new width and
        $dimensions = $this->getDimensions($srcImageResource, $maxDimension);

        // Create an image resource for the destination image.
        $dstImageResource = $this->createDestinationImageResource($srcImageResource, $dimensions);

        // Get the string contents of the destination image.
        $result = $this->imageResourceToString($dstImageResource);

        // Destroy the source and destination image resources.
        imagedestroy($srcImageResource);
        imagedestroy($dstImageResource);

        return $result;
    }

    /**
     * @param $imageResource
     * @param int $maxDimension
     * @return array
     */
    protected function getDimensions($imageResource, int $maxDimension): array
    {
        // Get the original width and height of the source image.
        $oldWidth = imagesx($imageResource);
        $oldHeight = imagesy($imageResource);

        if ($oldWidth > $oldHeight) {
            $newWidth = $maxDimension;
            $newHeight = $oldHeight * ($maxDimension / $oldWidth);
        } elseif ($oldWidth < $oldHeight) {
            $newWidth = $oldWidth * ($maxDimension / $oldHeight);
            $newHeight = $maxDimension;
        } else {
            // Else, both width and height must be equal.
            $newWidth = $maxDimension;
            $newHeight = $maxDimension;
        }

        return [
            'oldWidth' => $oldWidth,
            'oldHeight' => $oldHeight,
            'newWidth' => $newWidth,
            'newHeight' => $newHeight,
        ];
    }

    /**
     * @param $srcImageResource
     * @param array $dimensions
     * @return resource
     */
    protected function createDestinationImageResource($srcImageResource, array $dimensions)
    {
        // Create an image resource for the destination image.
        $dstImageResource = imagecreatetruecolor($dimensions['newWidth'], $dimensions['newHeight']);

        // Preserve the alpha.
        imagealphablending($dstImageResource, false);
        imagesavealpha($dstImageResource, true);
        $transparent = imagecolorallocatealpha($dstImageResource, 255, 255, 255, 127);
        imagefilledrectangle($dstImageResource, 0, 0, $dimensions['newWidth'], $dimensions['newHeight'], $transparent);

        // Perform the image resizing on the destination image.
        imagecopyresampled(
            $dstImageResource,
            $srcImageResource,
            0,
            0,
            0,
            0,
            $dimensions['newWidth'],
            $dimensions['newHeight'],
            $dimensions['oldWidth'],
            $dimensions['oldHeight']
        );

        return $dstImageResource;
    }

    /**
     * Convert an image resource to a string.
     *
     * @param $imageResource
     * @return string
     */
    protected function imageResourceToString($imageResource): string
    {
        $tempStream = fopen('php://temp', 'r+');
        imagepng($imageResource, $tempStream, 8);
        rewind($tempStream);
        $contents = stream_get_contents($tempStream);
        fclose($tempStream);

        return $contents;
    }
}
