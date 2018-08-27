<?php
namespace TheAentMachine\Docker;

use Docker\API\Client;
use Docker\Docker;

class ImageService
{
    public static function getInternalPorts(string $imageName) : array
    {
        $docker = Docker::create();

        $hasImage = $docker->imageGet($imageName);

        if (!$hasImage) {
            $result = $docker->imageCreate($imageName, [
                'fromImage' => $imageName,
                //'fromSrc' => 'hub.docker.'
            ])
            ;
            echo $result->;
        }
/*
        {
            *
            *     @var string $fromImage Name of the image to pull. The name may include a tag or digest. This parameter may only be used when pulling an image. The pull is cancelled if the HTTP connection is closed.
     *     @var string $fromSrc Source to import. The value may be a URL from which the image can be retrieved or `-` to read the image from the request body. This parameter may only be used when importing an image.
     *     @var string $repo Repository name given to an image when it is imported. The repo may include a tag. This parameter may only be used when importing an image.
     *     @var string $tag Tag or digest. If empty when pulling an image, this causes all tags for the given image to be pulled.
     *     @var string $platform Platform in the format os[/arch[/variant]]
     * }*/

        $ports = $docker->imageInspect($imageName)->getConfig()->getExposedPorts();

        $finalPorts = [];
        foreach ($ports as $portStr => $obj) {
            // $portStr = "80/tcp". Let's remove the string by casting.
            $finalPorts[] = (int) $portStr;
        }
        return $finalPorts;
    }
}