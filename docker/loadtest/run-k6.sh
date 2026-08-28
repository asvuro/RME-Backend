#!/usr/bin/env bash
# Jalankan k6 load test terhadap stack php-fpm+nginx di docker/loadtest.
# Pakai --user host supaya --summary-export bisa nulis file (image k6 jalan
# sebagai uid 12345, beda dengan pemilik folder scratch/loadtest di host).
#
# Contoh: TARGET_VUS=25 DURATION=60s ./docker/loadtest/run-k6.sh

set -euo pipefail
cd "$(dirname "$0")/../.."

BASE_URL="${BASE_URL:-http://127.0.0.1:8090}"
TARGET_VUS="${TARGET_VUS:-10}"
DURATION="${DURATION:-60s}"
OUT="${OUT:-scratch/loadtest/result_$(date +%s).json}"

docker run --rm --network host \
  --user "$(id -u):$(id -g)" \
  -v "$(pwd)/scratch/loadtest:/scripts" \
  -e BASE_URL="$BASE_URL" \
  -e TARGET_VUS="$TARGET_VUS" \
  -e DURATION="$DURATION" \
  grafana/k6 run --summary-export="/scripts/$(basename "$OUT")" /scripts/scenario.js
