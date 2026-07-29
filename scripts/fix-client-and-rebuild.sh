#!/bin/bash
set -euo pipefail
cd /root/opnform-test

APP_URL="http://10.2.0.44:8080"
FRONT_SECRET="$(grep -E '^FRONT_API_SECRET=' api/.env 2>/dev/null | cut -d= -f2- || true)"
if [ -z "$FRONT_SECRET" ]; then
  FRONT_SECRET="$(openssl rand -hex 24)"
  if grep -q '^FRONT_API_SECRET=' api/.env; then
    sed -i "s|^FRONT_API_SECRET=.*|FRONT_API_SECRET=${FRONT_SECRET}|" api/.env
  else
    echo "FRONT_API_SECRET=${FRONT_SECRET}" >> api/.env
  fi
fi

# Critical: private API must go through nginx (HTTP), never raw php-fpm :9000
cat > client/.env <<EOF
NUXT_PUBLIC_APP_URL=${APP_URL}
NUXT_PUBLIC_API_BASE=/api
NUXT_PRIVATE_API_BASE=http://opnform-test-ingress/api
NUXT_PUBLIC_ENV=production
NUXT_API_SECRET=${FRONT_SECRET}
NUXT_PUBLIC_LICENSE_API_ENDPOINT=https://api.opnform.com
EOF

echo "=== client env ==="
cat client/.env

echo "=== recreate client with fixed private API base ==="
docker compose -f docker-compose.test.yml up -d --no-deps --force-recreate ui
docker compose -f docker-compose.test.yml restart ingress
sleep 6

echo "=== private flags from client container ==="
docker exec opnform-test-client sh -c 'wget -q -O- --timeout=8 http://opnform-test-ingress/api/content/feature-flags' | head -c 400
echo

echo "=== building fork client image ==="
df -h / | tail -1
DOCKER_BUILDKIT=1 docker compose -f docker-compose.test.yml build ui
docker compose -f docker-compose.test.yml up -d --no-deps ui
docker compose -f docker-compose.test.yml restart ingress
sleep 8
curl -sS -m 10 -o /dev/null -w 'login=%{http_code}\n' http://127.0.0.1:8080/login
docker compose -f docker-compose.test.yml ps
