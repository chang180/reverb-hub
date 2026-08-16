# 客戶端從 Ably 改連 Reverb Hub

Hub **不**處理你專案的使用者登入。Private / Presence channel 授權仍在各客戶端的 `/broadcasting/auth`。

Hostinger 共享空間不必跑 `php artisan reverb:start`，只要能對 Hub 發 HTTP，且瀏覽器能連 `wss://你的-hub-網域`。

## 後端

安裝 Reverb 套件（只當 broadcaster，不啟動 server）：

```bash
composer require laravel/reverb
php artisan reverb:install
```

`.env`：

```ini
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=從 Hub 管理頁複製
REVERB_APP_KEY=從 Hub 管理頁複製
REVERB_APP_SECRET=建立或輪替時顯示的 secret

REVERB_HOST=你的-hub-網域
REVERB_PORT=443
REVERB_SCHEME=https
```

## 前端

拿掉 `@ably/laravel-echo`，改用：

```bash
npm install laravel-echo pusher-js
```

```js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

Vite：

```ini
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

Hub 管理頁的 Allowed origins 請填客戶端實際網域（例如 `https://your-site.com`）。
