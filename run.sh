#!/bin/bash

if [ $# -lt 1 ]; then
    echo "Usage: ./run.sh <users_file_path> <output_directory>" >&2
    exit 1
fi

# Define the main variables
scriptPath="../build/index.php"
usersFilePath=$1
indexerPath=$2

# Check if the script exists
if [ ! -f "$scriptPath" ]; then
    echo "The script '$scriptPath' does not exist." >&2
    exit 1
fi

# Check if the indexer path exists
if [ ! -f "$indexerPath" ]; then
    echo "The script '$indexerPath' does not exist." >&2
    exit 1
fi

# Execute the PHP script
if php "$scriptPath" "$usersFilePath" "$indexerPath"; then
    echo "BloxFinder's execution ended."
else
    echo "An error occurred while executing BloxFinder." >&2
    exit 1
fi