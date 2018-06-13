<?php


namespace TheAentMachine\Registry;

use GuzzleHttp\Client;

class RegistryClient
{
    /**
     * Returns the list of tags available for an image stored on Docker Hub.
     *
     * @return string[]
     */
    public function getImageTagsOnDockerHub(string $image): array
    {
        $client = new Client();
        $res = $client->request('GET', 'https://registry.hub.docker.com/v1/repositories/'.$image.'/tags');

        $response = \GuzzleHttp\json_decode($res->getBody(), true);

        $tags = \array_map(function (array $item) {
            return $item['name'];
        }, $response);

        return $tags;
    }
}
