#!/usr/bin/env bash
set -euo pipefail
echo '' >> .env
echo '' >> .env
echo '# Salts for WordPress generated on ' "$(date)"' - do not edit manually' >> .env
curl -s https://api.wordpress.org/secret-key/1.1/salt/ \
  | sed -E "s/^define\('([^']+)',[[:space:]]*'(.*)'\);$/\1='\2'/" \
  >> .env # config/salts.env
echo "Salts written to .env file."
