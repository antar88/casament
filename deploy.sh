#!/bin/bash

# Define the source directory (current directory) and the target directory
src_dir="$PWD"
target_dir="/var/www/html"

# Loop over all files in the source directory
for file in "$src_dir"/*; do
  # If this is not the documentation directory, move it to the target directory
  if [ "$file" != "$src_dir/documentation" ]; then
    sudo cp -Rf "$file" "$target_dir"
  fi
done

sudo systemctl restart apache2