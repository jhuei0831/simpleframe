### 系統資訊

* 開發環境

<table>
    <tr>
        <td>PHP</td>
        <td>7.4.13</td>
    </tr>
    <tr>
        <td>MariaDB</td>
        <td>10.4.17</td>
    </tr>
    <tr>
        <td>Apache</td>
        <td>2.4.46</td>
    </tr>
</table>

---

### 1. 安裝composer套件

```
composer install
```

### 2. 設定環境變數

* 複製設定檔

```
/* linux */
cp .env.example .env 

/* windows */
copy .env.example .env 
```

* 編輯設定檔 `.env`

### 3. 安裝npm套件

```
npm install
```

### 4. 建立tailwind css

開發時使用:
```
npm run build:tailwind-dev
```
開發完成後使用:
```
npm run build:tailwind-prod
```
* tailwind css相關設定到 `tailwind.config.js`設定
* 設定tailwind css 檔案輸出路徑請到 `package.json` 的 `scripts` 設定

### 5. 設定webpack

開發時使用:

```
npm run watch
```

開發完成後使用:

```
npm run serve
```

* webpack 相關設定請到 `webpack.config.js` 設定

### 6. setting.php

* _config/setting.php 中可以設定允許看見錯誤訊息的ip清單

### 7. .htaccess

* RewriteBase 你的路徑

### 8. 建立資料庫及資料

```
vendor\bin\phinx migrate
vendor\bin\phinx seed:run
```
