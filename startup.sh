#!/bin/sh
# Update packages
apt-get update

# Install dependencies
apt-get install -y git nodejs npm nginx

# Copy Nginx config from wwwroot (Make sure nginx.conf is present in wwwroot)
cp /home/site/wwwroot/nginx.conf /etc/nginx/sites-available/default

# Restart Nginx to apply the new config
service nginx restart

# Install npm packages if package.json exists
if [ -f "/home/site/wwwroot/package.json" ]; then
  cd /home/site/wwwroot
  npm install
fi

# Start the app (modify based on your app type)
exec "$@"