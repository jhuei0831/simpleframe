<?php

namespace App\Services\Twig;

use DebugBar\StandardDebugBar;
use Kerwin\Core\Support\Toolbox;
use Kerwin\Core\Support\Facades\Auth;
use Kerwin\Core\Support\Facades\Message;
use Kerwin\Core\Support\Facades\Permission;
use Kerwin\Core\Support\Facades\Role;
use Kerwin\Core\Support\Facades\Session;
use Kerwin\Core\Support\Facades\Request;

class LayoutExtension extends \Twig\Extension\AbstractExtension implements \Twig\Extension\GlobalsInterface
{
    private $debugbarRenderer;

    public function __construct() {
        $debugbar = new StandardDebugBar();
        $this->debugbarRenderer = $debugbar->getJavascriptRenderer(APP_ADDRESS.'vendor/maximebf/debugbar/src/Debugbar/Resources');
    }

    public function getFunctions()
    {
        return [
            new \Twig\TwigFunction('auth_user', [$this, 'authUser']),
            new \Twig\TwigFunction('breadcrumb', [$this, 'breadcrumb'], ['is_safe' => ['html']]),
            new \Twig\TwigFunction('debug_bar_render', [$this, 'render'], ['is_safe' => ['html']]),
            new \Twig\TwigFunction('debug_bar_renderHead', [$this, 'renderHead'], ['is_safe' => ['html']]),
            new \Twig\TwigFunction('in_except_ip_list', [$this, 'inExceptIpList']),
            new \Twig\TwigFunction('permission_can', [$this, 'permissionCan']),
            new \Twig\TwigFunction('header', [$this, 'header']),
            new \Twig\TwigFunction('role_has', [$this, 'roleHas']),
            new \Twig\TwigFunction('request_server', [$this, 'requestServer']),
            new \Twig\TwigFunction('session_get', [$this, 'sessionGet']),
            new \Twig\TwigFunction('show_flash_message', [$this, 'showFlashMessage']),
        ];
    }

    public function getGlobals(): array
    {
        $definedConstants = get_defined_constants(true)['user'];
        $constants = [];
        foreach ($definedConstants as $key => $value) {
            $constants[$key] = $value;
        }

        return $constants;
    }

    public function getOperators()
    {
        return [
            [
                '!' => ['precedence' => 50, 'class' => \Twig\Node\Expression\Unary\NotUnary::class],
            ],
            [
                '||' => ['precedence' => 10, 'class' => \Twig\Node\Expression\Binary\OrBinary::class, 'associativity' => \Twig\ExpressionParser::OPERATOR_LEFT],
                '&&' => ['precedence' => 15, 'class' => \Twig\Node\Expression\Binary\AndBinary::class, 'associativity' => \Twig\ExpressionParser::OPERATOR_LEFT],
            ],
        ];
    }
    
    /**
     * 取得當前登入使用者資料
     *
     * @return object
     */
    public function authUser()
    {
        return Auth::user();
    }
    
    /**
     * 麵包屑導航
     *
     * @param  string $home
     * @param  array  $breadcrumbs
     * @return void
     */
    public function breadcrumb(string $home, array $breadcrumbs)
    {
        return Toolbox::breadcrumb($home, $breadcrumbs);
    }
    
    /**
     * 設定header
     *
     * @param  string $args
     * @return void
     */
    public function header(string $args)
    {
        return header($args);
    }
    
    /**
     * 是否在IP例外清單中
     *
     * @return bool
     */
    public function inExceptIpList()
    {
        if (in_array($_SERVER["REMOTE_ADDR"], EXCEPT_IP_LIST)) {
            return true;
        }
        else {
            return false;
        }
    }
    
    /**
     * 輸出Debug Bar
     *
     * @return void
     */
    public function render()
    {
        return $this->debugbarRenderer->render();
    }
    
    /**
     * 輸出Debug Bar Header
     *
     * @return void
     */
    public function renderHead()
    {
        return $this->debugbarRenderer->renderHead();
    }
    
    /**
     * 是否擁有特定權限
     *
     * @param  string $permission
     * @return bool
     */
    public function permissionCan(string $permission)
    {
        return Permission::can($permission);
    }
    
    /**
     * 是否擁有特定角色
     *
     * @param  string $role
     * @return bool
     */
    public function roleHas(string $role)
    {
        return Role::has($role);
    }
    
    /**
     * 取得$_SERVER
     *
     * @param  string $name
     * @return string|array
     */
    public function requestServer(string $name)
    {
        $request = Request::createFromGlobals();
        return $request->server->get($name);
    }
    
    /**
     * 取得$_SESSION[$_ENV['APP_FOLDER']]
     *
     * @param  string $name
     * @return string|array
     */
    public function sessionGet(string $name)
    {
        return Session::get($name);
    }
    
    /**
     * 顯示Flash Message
     *
     * @return void
     */
    public function showFlashMessage()
    {
        return Message::showFlash();
    }
}
    