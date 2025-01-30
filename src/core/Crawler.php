<?php

/**
 * @license MIT
 * @copyright 2025 Matéo Florian CALLEC
 * 
 * @version 1.2.0
 */



/**
 * Class Crawler
 * 
 * Handles fetching data from the Roblox API, specifically retrieving friends of a user.
 */
class Crawler
{
    /**
     * @var string Base API URL for fetching friend data.
     */
    private $apiUrl = 'https://friends.roblox.com/v1/users';

    /**
     * Fetch the friends of a given Roblox user ID.
     *
     * @param int $userId The Roblox user ID.
     * @return array An array of friends' data or an error message.
     */
    public function getFriends(int $userId): array
    {
        $url = "{$this->apiUrl}/$userId/friends";

        $response = $this->makeRequest($url);

        if ($response === null)
        {
            return ['error' => "Unable to fetch friends for user ID $userId."];
        }

        return $response['data'] ?? [];
    }


    /**
     * Make a GET request to the specified URL.
     *
     * @param string $url The URL to fetch.
     * @return array|null The decoded JSON response or null on failure.
     */
    private function makeRequest(string $url): ?array
    {
        $result = $this->get_remote_contents($url);

        return json_decode($result, true);
    }


    /**
     * Fetch remote content from a given URL with retry logic.
     *
     * @param string $url The URL to fetch.
     * @return string The response content.
     */
    public function get_remote_contents(string $url): string
    {
        $need_sleep = false;

        do
        {
            $response = file_get_contents($url);
            
            if ($need_sleep === true)
            {
                /**
                 * If the response fails, retry in 10 seconds.
                 */
                print('Waiting...' . PHP_EOL);

                sleep(10);
            }

            $need_sleep = true;
        } while($response === false);

        return $response;
    }
}


// Example usage:
//$crawler = new Crawler();
//$friends = $crawler->getFriends(123456789); // Replace with a valid Roblox user ID
//print_r($friends);


?>