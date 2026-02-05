#!/usr/bin/env bash
set -euo pipefail

CMD=${1:-}
POST_NAME=${2:-}

if [[ -z "$CMD" ]]; then
  echo "Usage: $0 <reset|list> [post_name]"
  exit 1
fi

DB_HOST=${DB_HOST:-127.0.0.1}

case "$CMD" in
  reset)
    if [[ -z "$POST_NAME" ]]; then
      echo "Usage: $0 reset <post_name>"
      exit 1
    fi
    SQL=$(cat <<'EOSQL'
START TRANSACTION;
DELETE pm
  FROM wp_postmeta pm
  JOIN wp_posts p ON pm.post_id = p.ID
 WHERE p.post_type = 'wp_block'
   AND p.post_name = '%POST_NAME%';
DELETE r
  FROM wp_posts r
  JOIN wp_posts p ON r.post_parent = p.ID
 WHERE p.post_type = 'wp_block'
   AND p.post_name = '%POST_NAME%'
   AND r.post_type = 'revision';
DELETE FROM wp_posts
 WHERE post_type = 'wp_block'
   AND post_name = '%POST_NAME%';
COMMIT;
EOSQL
)
    SQL=${SQL//%POST_NAME%/$POST_NAME}
    DB_HOST="$DB_HOST" composer wp db query "$SQL"
    ;;
  list)
    DB_HOST="$DB_HOST" composer wp db query "SELECT ID, post_title, post_name, post_status, post_date FROM wp_posts WHERE post_type='wp_block';"
    ;;
  *)
    echo "Unsupported command: $CMD"
    echo "Supported commands: reset, list"
    exit 1
    ;;
esac
