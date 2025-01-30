<?php

/**
 * @license MIT
 * @copyright 2025 Matéo Florian CALLEC
 * 
 * @version 1.2.0
 */



/**
 * Check if the users file path is provided as a command-line argument.
 */
if (isset($argv[1]) && is_int((int)$argv[1]))
{
    $usersFilePath = $argv[1];
} else {
    print('No users file path provided.' . PHP_EOL);
}


/**
 * Check if the indexer path is provided as a command-line argument.
 */
if (isset($argv[2]) && !empty($argv[2]) && is_string($argv[2]))
{
    $indexer_path = $argv[2];
} else {
    print('No indexer path provided.' . PHP_EOL);
}


/**
 * Exit if required arguments are missing.
 */
if (!isset($indexer_path) || !isset($usersFilePath))
{
    exit(0);
}


// Load required class files
require_once(__DIR__ . '/core/Crawler.php');
require_once(__DIR__ . '/core/Indexer.php');


// Initialize the crawler and indexer instances
$crawler = new Crawler();
$indexer = new Indexer($indexer_path);

// Read user IDs from the input file
$userIdsList = str_replace(' ', '', file_get_contents($usersFilePath));


/**
 * Check if the file exists and contains data.
 */
if (!$userIdsList || empty($userIdsList))
{
    print('File empty or not found.' . PHP_EOL);
    exit(0);
}

$userIds = [];

// Read file line by line, ignoring empty lines and comments
$lines = file($usersFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line)
{
    /**
     * Ignore empty lines and lines starting with ';' (treated as comments).
     */
    if (trim($line) !== '' && strpos(trim($line), ';') !== 0)
    {
        $userIds[] = $line;
    }
}


/**
 * Process each user ID:
 * - Fetch friends of the user.
 * - Index user information.
 * - Index friends of the user.
 */
foreach ($userIds as $userId)
{
    $users = $crawler->getFriends((int) $userId);

    // Index main user info
    $indexer->indexUsers(array($indexer->getUserInfo($userId)));

    // Index friends of the user
    $indexer->indexUsers($users);
}


?>