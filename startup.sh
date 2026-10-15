#!/bin/sh
# Update packages
apt-get update

# Install dependencies
 apt-get install -y git nginx curl ca-certificates

# Add the NodeSource repository for Node.js 22
 mkdir -p /etc/apt/keyrings
curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key \
  |  gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg

echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_22.x nodistro main" \
  |  tee /etc/apt/sources.list.d/nodesource.list

# Install Node.js 22 (includes npm)
 apt-get update
 apt-get install -y nodejs
apt-get install -y nginx

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