<?php

    namespace App\Models\Twig;

    use DebugBar\StandardDebugBar;
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

        public function authUser()
        {
            return Auth::user();
        }

        public function header(string $args)
        {
            return header($args);
        }

        public function inExceptIpList()
        {
            if (in_array($_SERVER["REMOTE_ADDR"], EXCEPT_IP_LIST)) {
                return true;
            }
            else {
                return false;
            }
        }

        public function render()
        {
            return $this->debugbarRenderer->render();
        }

        public function renderHead()
        {
            return $this->debugbarRenderer->renderHead();
        }

        public function permissionCan(string $permission)
        {
            return Permission::can($permission);
        }

        public function roleHas(string $role)
        {
            return Role::has($role);
        }

        public function requestServer(string $name)
        {
            $request = Request::createFromGlobals();
            return $request->server->get($name);
        }

        public function sessionGet(string $name)
        {
            return Session::get($name);
        }

        public function showFlashMessage()
        {
            return Message::showFlash();
        }
    }
    