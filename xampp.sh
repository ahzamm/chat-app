#!/bin/bash

# Stop any running web servers (Apache or Nginx)
echo "Stopping Apache if it's running..."
sudo systemctl stop apache2

echo "Stopping Nginx if it's running..."
sudo systemctl stop nginx

# Start XAMPP
echo "Starting XAMPP..."
sudo /opt/lampp/lampp start

# Check if XAMPP started successfully
if [ $? -eq 0 ]; then
    echo "XAMPP started successfully!"
else
    echo "There was an issue starting XAMPP."
fi
