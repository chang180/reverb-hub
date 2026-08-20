---
paths:
  - 'docker-compose*.yml'
  - 'docker/**'
  - '.env.example'
  - '.env.mac.example'
  - 'README.md'
---

# Local Docker environments

macOS uses OrbStack with `.env.mac.example` (`HTTP_BIND=127.0.0.1`, `HTTP_PORT=9080`, `COMPOSE_PROJECT_NAME=reverb-hub-orb`, `CADDY_SITE=:80`). Do not bind host 80/443 on Mac — Laravel Herd owns those. Do not put host `9080` in `CADDY_SITE` (that port is Caddy's listen port inside the container; Docker maps 9080->80). Prefer `http://caddy.reverb-hub-orb.orb.local`. Do not use `http://reverb-hub.test`. Local Redis is the `redis` service in `docker-compose.local.yml` (`REDIS_HOST=redis`); do not publish 6379 (Herd Redis). VPS uses the host Redis daemon (`REDIS_HOST=host.docker.internal`) and must not load `docker-compose.local.yml`. WSL uses `.env.example` and `CADDY_SITE=http://localhost` on port 80.
