<?php declare(strict_types=1);

use App\Models\Auth\User;
use Dotenv\Dotenv;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Kerwin\Core\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

final class LoginTest extends TestCase
{
    public $client;
    public $session;
    public $user;

    protected function setUp(): void
    {
        $this->user = new User(); 
        $dotenv = Dotenv::createImmutable(__DIR__.'/..');
        $dotenv->load();
        $baseUri = Config::getAppAddress();
        $this->client = new Client(['base_uri' => $baseUri]);
        $this->session = new Session(new PhpBridgeSessionStorage());
    }

    public function testCanRenderLoginPage(): void
    {
        $response = $this->client->request('GET', 'auth/login.php');
        
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testLoginWithErrorCaptcha(): void
    {
        $request = [
            'captcha' => 12345,
            'email' => 'admin@gmail.com',
            'password' => 'password'
        ];
        $login = $this->user->login($request);
        $this->assertStringContainsString('驗證碼錯誤', $login['msg']);
    }

    public function testLoginWithLoginFailed(): void
    {
        $request = [
            'captcha' => $this->session->get('captcha'),
            'email' => 'admin@gmail.com',
            'password' => 'Error_password'
        ];
        $login = $this->user->login($request);
        $this->assertStringContainsString('登入失敗', $login['msg']);
    }

    public function testLoginWithLoginSuccess(): void
    {
        $request = [
            'captcha' => $this->session->get('captcha'),
            'email' => 'admin@gmail.com',
            'password' => 'password'
        ];
        $login = $this->user->login($request);
        $this->assertStringContainsString('登入成功', $login['msg']);
    }
}