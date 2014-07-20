<?php
namespace image_manipulator;

use image_manipulator\ImageManipulator;

class Image
{
    public static function process($source, $destination, $newWidth, $newHeight)
    {
        if ($newWidth > 0 && $newHeight > 0) {
            $sizeMessage = "Uploaded image must be at least {$newWidth}px by {$newHeight}px.";
        } else if ($newWidth == 0) {
            $sizeMessage = "Uploaded image must be at least {$newHeight}px tall.";
        } else if ($newHeight == 0) {
            $sizeMessage = "Uploaded image must be at least {$newWidth}px wide.";
        }

        try {
            $img = new ImageManipulator($source['tmp_name']);
        } catch (FileNotFoundException $e) {
            throw new \Exception(
                'Uploaded image must be JPG or PNG. ' .
                'If it was, try again or notify a webmaster of the problem.'
            );
        }

        try {
            $img->cropToFitAndResize($newWidth, $newHeight);
        } catch (LargerThanSourceException $e) {
            throw new \Exception(
                $sizeMessage .  ' Try again with a larger image.'
            );
        }
        
        @unlink($destination);

        try {
            $img->saveJPG($destination);
        } catch (CouldNotSaveFileException $e) {
            throw new \Exception(
                'Uploaded image could not be saved. Notify the webmaster of the problem.'
            );
        }
        
        return true;
    }
}
?>
