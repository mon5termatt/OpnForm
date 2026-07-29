#!/bin/bash
set -euo pipefail
cd /root/opnform-test

APP_URL="http://10.2.0.44:8080"
FRONT_SECRET="$(openssl rand -hex 24)"
JWT_SECRET="$(openssl rand -hex 32)"
APP_KEY="base64:$(openssl rand -base64 32)"

cp -f api/.env.example api/.env

sed -i \
  -e "s|^APP_ENV=.*|APP_ENV=production|" \
  -e "s|^APP_DEBUG=.*|APP_DEBUG=false|" \
  -e "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" \
  -e "s|^APP_URL=.*|APP_URL=${APP_URL}|" \
  -e "s|^FRONT_URL=.*|FRONT_URL=${APP_URL}|" \
  -e "s|^FRONT_API_SECRET=.*|FRONT_API_SECRET=${FRONT_SECRET}|" \
  -e "s|^DB_HOST=.*|DB_HOST=db|" \
  -e "s|^DB_DATABASE=.*|DB_DATABASE=forge|" \
  -e "s|^DB_USERNAME=.*|DB_USERNAME=forge|" \
  -e "s|^DB_PASSWORD=.*|DB_PASSWORD=forge|" \
  -e "s|^REDIS_HOST=.*|REDIS_HOST=redis|" \
  -e "s|^CACHE_STORE=.*|CACHE_STORE=redis|" \
  -e "s|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=redis|" \
  -e "s|^SESSION_DRIVER=.*|SESSION_DRIVER=redis|" \
  -e "s|^JWT_SECRET=.*|JWT_SECRET=${JWT_SECRET}|" \
  api/.env

if grep -q '^SELF_HOSTED=' api/.env; then
  sed -i 's|^SELF_HOSTED=.*|SELF_HOSTED=true|' api/.env
else
  echo 'SELF_HOSTED=true' >> api/.env
fi

cat > client/.env <<EOF
NUXT_PUBLIC_APP_URL=${APP_URL}
NUXT_PUBLIC_API_BASE=${APP_URL}/api
NUXT_PRIVATE_API_BASE=http://api:9000
NUXT_PUBLIC_ENV=production
NUXT_API_SECRET=${FRONT_SECRET}
NUXT_PUBLIC_LICENSE_API_ENDPOINT=https://api.opnform.com
EOF

echo "=== api env ==="
grep -E '^(APP_URL|FRONT_URL|SELF_HOSTED|DB_HOST|REDIS_HOST|CACHE_STORE|QUEUE_CONNECTION)=' api/.env
echo "=== client env ==="
cat client/.env

echo "=== pull deps + build API ==="
docker compose -f docker-compose.test.yml pull db redis ingress ui
DOCKER_BUILDKIT=1 docker compose -f docker-compose.test.yml build api

echo "=== up ==="
docker compose -f docker-compose.test.yml up -d
sleep 8
docker compose -f docker-compose.test.yml ps
echo "=== restart ingress ==="
docker compose -f docker-compose.test.yml restart ingress || true
sleep 5

echo "=== health probes ==="
for i in $(seq 1 30); do
  code=$(curl -sS -o /dev/null -w '%{http_code}' http://127.0.0.1:8080/login || echo 000)
  echo "try $i login=$code"
  if [ "$code" = "200" ]; then
    break
  fi
  sleep 5
done

echo "=== feature flags / license ==="
curl -sS http://127.0.0.1:8080/api/content/feature-flags | head -c 1200; echo
echo "=== bound LicenseService class ==="
docker exec opnform-test-api php -r 'require "/usr/share/nginx/html/vendor/autoload.php"; $app=require "/usr/share/nginx/html/bootstrap/app.php"; $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo get_class(app(App\Service\License\LicenseService::class)), PHP_EOL;'
