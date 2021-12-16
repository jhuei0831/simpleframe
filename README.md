# 目錄
1. [系統資訊](#系統資訊)
2. [Database](#database)
    1. [介紹](#介紹)
    2. [指令](#指令)
3. [Container](#container)
4. [Controller](#controller)
5. [Model](#model)
    1. [介紹](#model_intro)
    2. [設定主鍵](#model_key)
    3. [將Models加入Container](#model_container)
    4. [功能](#model_fun)
6. [View](#view)
    1. [介紹](#view_intro)
    1. [LayoutExtension](#view_ex)
    2. [應用](#view_app)
7. [Route](#route)
    1. [介紹](#route_intro)
    2. [應用](#route_app)
8. [Middleware](#middleware)
    1. [介紹](#middleware_intro)
    2. [應用](#middleware_app)
9. [Log](#log)
    1. [介紹](#log_intro)
    2. [應用](#log_app)

# 系統資訊
<span style="font-size:4px">[🔼](#)</span>
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

### 1. 安裝框架

```
composer create-project kerwin/simpleframe simpleframe --repository="{\"type\": \"vcs\",\"url\": \"https://github.com/jhuei0831/simpleframe.git\"}"
```

* 編輯設定檔 `.env`

### 2. 安裝npm套件

```
npm install
```

### 3. 建立tailwind css

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

### 4. 設定webpack

開發時使用:

```
npm run watch
```

開發完成後使用:

```
npm run serve
```

* webpack 相關設定請到 `webpack.config.js` 設定

### 5. setting.php

* config/setting.php 中可以設定允許看見錯誤訊息的ip清單

### 6. .htaccess

* RewriteBase 你的路徑

### 7. 建立資料庫及資料

```
vendor\bin\phinx migrate
vendor\bin\phinx seed:run
```

### 8. 降php版本

* 在composer.json中加入，以降至7.2版本為例
```
"config": {
    "platform": {
        "php": "7.2.0"
    }
}
```
* 刪除`composer.lock`及`/vender`後執行`composer update`

---

# Database 
<span style="font-size:4px">[🔼](#)</span>

## <a name="db_intro">介紹</a>
使用[robmorgan/phinx](https://book.cakephp.org/phinx/0/en/index.html)，透過指令的方式將資料表建立或加入測試資料。

資料表schema放在`/database/migrations`底下，資料建立放在`/database/seeds`底下。

> 如果要更改放置位子可以到`/phinx.php`中修改

phinx.php
```php
<?php
..省略...

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/database/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/database/seeds'
    ],
    ...省略...
];
```

## <a name="db_cmd">指令</a>

### 建立migration

```cmd
vendor\bin\phinx create MyNewMigration
```
詳細請參考[這裡](https://book.cakephp.org/phinx/0/en/commands.html)

---

# Container 
<span style="font-size:4px">[🔼](#)</span>

在`app\config.php`中加入設定並透過`app\bootstrap.php`執行。

相關設定請參考: [https://php-di.org/](https://php-di.org/)

---

# Controller 
<span style="font-size:4px">[🔼](#)</span>

放在`app\Http\Controller`底下，function可以使用加入`Container`的類別(class)

```php
<?php

namespace App\Http\Controller\Manage;

use Twig\Environment;

class UserController
{
    /**
     * 使用者管理頁面
     *
     * @return void
     */
    public function index(Environment $twig)
    {
        echo $twig->render('manage/users/index.twig');
    }
}

```

---

# Model 
<span style="font-size:4px">[🔼](#)</span>

## <a name="model_intro">介紹</a>
放在`app\Models`底下，必須要加入Container，可以對。

```php
<?php

namespace App\Models;

use Kerwin\Core\Model;

class User extends Model
{
    
}
```

## <a name="model_key">設定主鍵</a>
```php
$primaryKey = 'id';
```

## <a name="model_container">將Models加入Container</a>
```php
// Models
User::class => create(User::class)
```
## <a name="model_fun">功能</a>

### all
回傳全部列數資料
```php
public function index(User $user)
{
    return $user->all();
}
```

### find
回傳特定主鍵資料
```php
public function show(User $user, $id)
{
    return $user->find($id);
}
```

### insert
新增資料
```php
public function store(User $user)
{
    return $user->insert(['name' => 'Jack', 'email' => 'jack@simpleframe.com']);
}
```

### update
更新資料
```php
public function update(User $user, $id)
{
    return $user->update($id, ['name' => 'Jack', 'email' => 'LoveRose@simpleframe.com']);
}
```

### delete
刪除資料
```php
public function delete(User $user, $id)
{
    return $user->delete($id);
}
```

---

# View 
<span style="font-size:4px">[🔼](#)</span>

## <a name="view_intro">介紹</a>
View是使用[Twig](https://twig.symfony.com/)，透過[Controller](#controller)和[Route](#route)將View呈現出來

## <a name="view_ex">LayoutExtension</a>
### 設定View Extension

SimpleFrame已經做一些基本的設定，位於`App\Services\Twig\LayoutExtension.php`，相關設定可以參考[這裡](https://twig.symfony.com/doc/3.x/advanced.html)。

## <a name="view_app">應用</a>

```php
public function index(User $user)
{
    $users = $user->all();

    echo $this->twig->render('index.twig', [
        'users' => $users
    ]);
}
```

### index.twig
```twig
{% for user in users %}

名字: {{ user.name }}
信箱: {{ user.email }}

{% endfor %}
```

---

# Route 
<span style="font-size:4px">[🔼](#)</span>

## <a name="route_intro">介紹</a>
路由是使用[nikic/FastRoute](https://github.com/nikic/FastRoute)為基底做修改，在`simpleframe/index.php`中設定

## <a name="route_app">應用</a>
```php
<?php
$root = "./";
include($root.'config/settings.php');

use Kerwin\Core\Router\RouteCollector;
use function Kerwin\Core\Router\simpleDispatcher;

$container = require __DIR__ . '/app/bootstrap.php';

$dispatcher = simpleDispatcher(function (RouteCollector $route) {
    $route->addGroup('/simpleframe', function (RouteCollector $route) {
        # /simpleframe/
        $route->get('/', 'App\Http\Controller\HomeController');
        # /simpleframe/auth
        $route->addGroup('/auth', function (RouteCollector $route) {
            # /simpleframe/auth/login
            $route->get('/login', ['App\Http\Controller\Auth\LoginController', 'index']);
            $route->post('/login', ['App\Http\Controller\Auth\LoginController', 'login']);
        });
    });
});

$dispatcher->process($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $container);
```

---

# Middleware 
<span style="font-size:4px">[🔼](#)</span>

## <a name="middleware_intro">介紹</a>
1. 中間件必須建立在`App\Http\Middleware`底下，必須使用抽象類別`Kerwin\Core\Router\Middleware\Middleware`。

2. 建立中間件後必須要加入[Container](#container)。

2. 中間件設定必須加在`addRoute`或`addGroup`前方，如果加在`addGroup`則底下路由都會生效。

## <a name="middleware_app">應用</a>

### Middleware
```php
# 如果未登入，就導向404頁面

<?php

namespace App\Http\Middleware;

use Closure;
use Twig\Environment;
use Kerwin\Core\Request;
use Kerwin\Core\Support\Facades\Session;
use Kerwin\Core\Router\Middleware\Middleware;

class AuthMiddleware implements Middleware
{
    private $twig;

    public function __construct(Environment $twig) {
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Closure $next, $arg = NULL)
    {
        if (!Session::get('USER_ID')) {
            echo $this->twig->render('_error/404.twig');
            return;
        }

        return $next($request);
    }
}

```

### Container
```php
// Middleware
'auth' => function (Environment $twig) {
    return new App\Http\Middleware\AuthMiddleware($twig);
},
```

### Route
```php
$route->middleware('auth')->get('/user/info', ['App\Http\Controller\Auth\LoginController', 'index']);
```

---

# Log 
<span style="font-size:4px">[🔼](#)</span>

## <a name="log_intro">介紹</a>
使用`monolog`搭配自己建立的`PDOHandler`將Log資料儲存在資料庫中，已經有將Log加入[Container](#container)中

## <a name="log_app">應用</a>
```php
public function login(Request $request, Log $log): void
{
    ...省略...
    $log->info('登入成功');
    ...省略...
    $log->error('登入失敗', ['account' => $data['email']]);
}
```