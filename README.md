# Reverb Hub

獨立的 Laravel 13 Reverb 主機：核發多組 App / Key / Secret，讓 Hostinger 共享空間等無法長駐 WebSocket 的專案連進來廣播。

## 本機（macOS + OrbStack）

與 Windows/WSL、Laravel Herd 錯開：Herd 繼續佔 `80`/`443` 與 `*.test`。Mac 請走 OrbStack 網域（直連容器 80）；host `9080` 只是額外對應，不要寫進 `CADDY_SITE`。

請先安裝並啟動 [OrbStack](https://orbstack.dev/)，Docker context 選 OrbStack（不要用 Docker Desktop）。專案若放在 `~/Herd`，Herd 仍會掛出 `http://reverb-hub.test`——**不要用那個網址**，也不要用 Herd 啟動 PHP；改走下面的 Compose。

```bash
cd ~/Herd/reverb-hub
cp .env.mac.example .env
docker compose up -d --build
```

瀏覽器開（優先）：

- http://caddy.reverb-hub-orb.orb.local（OrbStack 自動網域，清單見 http://orb.local）
- http://127.0.0.1:9080 或 http://localhost:9080（host 埠對應，避開 Herd 的 80）

不要開 `http://localhost`（沒加埠會進 Herd）或 `http://reverb-hub.test`。

## 本機（WSL + Docker Desktop）

```bash
cd ~/reverb-hub
cp .env.example .env
docker compose -f docker-compose.yml -f docker-compose.local.yml up -d --build
```

若 WSL 出現 `docker-credential-desktop.exe: exec format error`：

```bash
mkdir -p /tmp/docker-nocreds
echo '{}' > /tmp/docker-nocreds/config.json
DOCKER_CONFIG=/tmp/docker-nocreds docker compose -f docker-compose.yml -f docker-compose.local.yml up -d --build
```

瀏覽器開 http://localhost

預設管理員（`database/seeders/DatabaseSeeder.php`）：

- Email: `admin@reverb-hub.test`
- Password: `password`

管理頁建立 Application 後，把 App ID / Key / Secret 交給客戶端專案。輪替 key 後執行：

```bash
docker compose restart reverb
```

## VPS（git pull + Docker）

第一次：

```bash
git clone git@github.com:chang180/reverb-hub.git
cd reverb-hub
cp .env.example .env
```

編輯 `.env`：

- `APP_URL=https://你的網域`
- `CADDY_SITE=你的網域`（不要加 `http://`，Caddy 會申請 Let's Encrypt）
- `REVERB_HOST=你的網域`
- `REVERB_PORT=443`
- `REVERB_SCHEME=https`
- `DB_PASSWORD`、`ADMIN_EMAIL`、`ADMIN_PASSWORD`
- `REDIS_HOST=host.docker.internal`（主機內建 Redis；不要用 `docker-compose.local.yml`）

然後：

```bash
docker compose up -d --build
docker compose exec app php artisan key:generate --force
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
```

之後更新：

```bash
git pull
docker compose up -d --build
docker compose exec app php artisan migrate --force
docker compose restart reverb
```

80 / 443 需對公網開放。Caddy 把 `/app`、`/apps` 轉到 Reverb，其餘進管理頁。

客戶端對接見 [docs/client-setup.md](docs/client-setup.md)。
