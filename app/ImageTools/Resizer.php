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

        // Get the original width and height of the source image.
        $oldWidth = imageSX($srcImageResource);
        $oldHeight = imageSY($srcImageResource);

        // Determine the new width and height.
        if ($oldWidth > $oldHeight) {
            $newWidth = $maxDimension;
            $newHeight = $oldHeight * ($maxDimension / $oldWidth);
        } elseif ($oldWidth < $oldHeight) {
            $newWidth = $oldWidth * ($maxDimension / $oldHeight);
            $newHeight = $maxDimension;
        } else {
            // Both are equal.
            $newWidth = $maxDimension;
            $newHeight = $maxDimension;
        }

        // Create an image resource for the destination image.
        $dstImageResource = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve the alpha.
        imagealphablending($dstImageResource, false);
        imagesavealpha($dstImageResource,true);
        $transparent = imagecolorallocatealpha($dstImageResource, 255, 255, 255, 127);
        imagefilledrectangle($dstImageResource, 0, 0, $newWidth, $newHeight, $transparent);

        // Perform the image resizing on the destination image.
        imagecopyresampled(
            $dstImageResource,
            $srcImageResource,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $oldWidth,
            $oldHeight
        );

        // Get the string contents of the destination image.
        $tempStream = fopen('php://temp', 'r+');
        imagepng($dstImageResource, $tempStream, 8);
        rewind($tempStream);
        $result = stream_get_contents($tempStream);
        fclose($tempStream);

        // Destroy the source and destination image resources.
        imagedestroy($srcImageResource);
        imagedestroy($dstImageResource);

        return $result;
    }
}
