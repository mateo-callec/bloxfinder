<?php

/**
 * @license MIT
 * @copyright 2025 Matéo Florian CALLEC
 * 
 * @version 1.2.0
 */



/**
 * Class Indexer
 * 
 * Indexes users by creating directories and storing profile information.
 */
class Indexer
{
    /**
     * @var string Base path for indexing users
     */
    private $basePath;


    /**
     * Constructor
     * 
     * @param string $path The base path where user directories will be created
     */
    public function __construct(string $path)
    {
        $this->basePath = rtrim($path, DIRECTORY_SEPARATOR);

        if (!is_dir($this->basePath))
        {
            mkdir($this->basePath, 0755, true);
        }
    }


    /**
     * Indexes a list of users.
     * 
     * @param array $users Array of users containing 'id', 'name', and 'displayName'.
     * @return void
     */
    public function indexUsers(array $users): void
    {
        foreach ($users as $user)
        {
            $usernameDir = $this->basePath . DIRECTORY_SEPARATOR . '@' . $user['name'];

            if (!is_dir($usernameDir))
            {
                print('Indexing: @' . $user['name'] . PHP_EOL);

                mkdir($usernameDir, 0755, true);
                $this->createProfileImage($usernameDir, $user['id']);
                $this->createProfileFile($usernameDir, $user);
            }
        }
    }


    /**
     * Downloads and saves the user's profile image.
     * 
     * @param string $path The directory where the image will be saved.
     * @param string $userId The user's ID.
     * @return void
     */
    private function createProfileImage(string $path, string $userId): void
    {
        $crawler = new Crawler();
        $imageTmpUrl = $crawler->get_remote_contents("https://thumbnails.roblox.com/v1/users/avatar-headshot?userIds=$userId&size=420x420&format=Png&isCircular=false&thumbnailType=HeadShot");

        if ($imageTmpUrl)
        {
            $imageUrl = json_decode($imageTmpUrl, true)['data'][0]['imageUrl'];

            if (!empty($imageUrl))
            {
                $imageData = $crawler->get_remote_contents($imageUrl);

                if ($imageData)
                {
                    $extension = 'png';
                    $filePath = $path . DIRECTORY_SEPARATOR . "$userId-profile.$extension";
                    file_put_contents($filePath, $imageData);
                }
            }
        }
    }


    /**
     * Creates a markdown profile file for the user.
     * 
     * @param string $path The directory where the profile file will be saved.
     * @param array $user User data containing 'id', 'name', and 'displayName'.
     * @return void
     */
    private function createProfileFile(string $path, array $user): void
    {
        $markdownContent = file_get_contents(__DIR__ . '/../assets/profile.md');

        $markdownContent = str_replace('[threat_level]', '#TL_none', $markdownContent);
        $markdownContent = str_replace('[primary_concern]', '#PC_none', $markdownContent);
        $markdownContent = str_replace('[surveillance_status]', '#SS_none', $markdownContent);

        $markdownContent = str_replace('[user_id]', $user['id'], $markdownContent);
        $markdownContent = str_replace('[username]', $user['name'], $markdownContent);
        $markdownContent = str_replace('[display_name]', $user['displayName'], $markdownContent);

        $user_info = $this->getUserInfo($user['id']);

        $markdownContent = str_replace('[description]', $user_info['description'], $markdownContent);
        $markdownContent = str_replace('[creation_date]', $user_info['created'], $markdownContent);
        $markdownContent = str_replace('[user_banned]', ($user_info['isBanned'] == true) ? '#RB_banned' : '#RB_not_banned', $markdownContent);

        $crawler = new Crawler();
        $friends = $crawler->getFriends($user['id']);

        $friendsList = '';
        foreach ($friends as $friend)
        {
            $friendsList .= '[[' . $friend['name'] . ']], ';
        }
        $friendsList = substr($friendsList, 0, -2);

        $markdownContent = str_replace('[friends_list]', $friendsList, $markdownContent);

        $filePath = $path . DIRECTORY_SEPARATOR . $user['name'] . '.md';
        file_put_contents($filePath, $markdownContent);
    }


    /**
     * Fetches user information from the Roblox API.
     * 
     * @param int $userId The user's ID.
     * @return array An associative array containing user details.
     */
    public function getUserInfo(int $userId): array{
        $crawler = new Crawler();

        return json_decode(
            $crawler->get_remote_contents('https://users.roblox.com/v1/users/' . $userId),
        true);
    }
}


// Example usage:
//$crawler = new Crawler();
//$users = $crawler->getFriends(123456789); // Replace with a valid user ID to get data
//$indexer = new Indexer('/path/to/index/directory');
//$indexer->indexUsers($users);


?>