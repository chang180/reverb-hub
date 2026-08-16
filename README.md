# Reverb Hub

獨立的 Laravel 13 Reverb 主機：核發多組 App / Key / Secret，讓 Hostinger 共享空間等無法長駐 WebSocket 的專案連進來廣播。

## 本機（WSL + Docker Desktop）

```bash
cd ~/reverb-hub
cp .env.example .env
docker compose up -d --build
```

若 WSL 出現 `docker-credential-desktop.exe: exec format error`：

```bash
mkdir -p /tmp/docker-nocreds
echo '{}' > /tmp/docker-nocreds/config.json
DOCKER_CONFIG=/tmp/docker-nocreds docker compose up -d --build
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
