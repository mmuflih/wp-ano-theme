#!/bin/bash

# Script to compress directory to zip excluding .git directory
# Usage: ./compress.sh [output_filename.zip]

# Set default output filename if not provided
OUTPUT_FILE="${1:-project.zip}"

# Check if zip command exists
if ! command -v zip &> /dev/null; then
    echo "Error: zip command not found. Please install zip first."
    exit 1
fi

# Get the current directory name
DIR_NAME=$(basename "$(pwd)")

echo "Compressing current directory to $OUTPUT_FILE..."
echo "Excluding .git directory..."

# Delete existing zip file if it exists
if [ -f "$OUTPUT_FILE" ]; then
    echo "Removing existing $OUTPUT_FILE..."
    rm "$OUTPUT_FILE"
fi

# Compress all files excluding .git directory
zip -r "$OUTPUT_FILE" . -x ".git/*" ".git/**/*" ".git/.*"

if [ $? -eq 0 ]; then
    echo "✓ Successfully created $OUTPUT_FILE"
    ls -lh "$OUTPUT_FILE.zip"
else
    echo "✗ Error creating zip file"
    exit 1
fi
