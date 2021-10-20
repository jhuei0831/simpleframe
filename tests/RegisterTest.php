<?php declare(strict_types=1);

use _models\Auth\User;
use Dotenv\Dotenv;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Kerwin\Core\Support\Facades\Database;
use Kerwin\Core\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

final class RegisterTest extends TestCase
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

    public function testCanRenderRegisterPage(): void
    {
        $response = $this->client->request('GET', 'auth/register.php');
        
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testRegisterWithErrorValidation(): void
    {
        $request = [
            'token'             => $this->session->get('token'),
            'name'              => 'test',
            'email'             => 'test@test.com',
            'password'          => 'password',
            'password_confirm'  => '',
        ];

        $register = $this->user->register($request);
        $this->assertStringContainsString('註冊失敗，請檢查輸入', $register['msg']);
    }

    public function testRegisterWithSuccess(): void
    {
        $request = [
            'token'             => $this->session->get('token'),
            'name'              => 'test',
            'email'             => 'test@test.com',
            'password'          => 'password',
            'password_confirm'  => 'password',
        ];
        $register = $this->user->register($request);
        $this->assertStringContainsString('註冊成功', $register['msg']);
    }

    public function testRegisterWithEmailDuplicate(): void
    {
        $request = [
            'token'             => $this->session->get('token'),
            'name'              => 'test',
            'email'             => 'test@test.com',
            'password'          => 'password',
            'password_confirm'  => 'password',
        ];
        $register = $this->user->register($request);
        $testUser = Database::table('users')->where("email='test@test.com'")->first();
        $this->user->delete($testUser->id);
        $this->assertStringContainsString('信箱已被註冊使用', $register['msg']);
    }
}