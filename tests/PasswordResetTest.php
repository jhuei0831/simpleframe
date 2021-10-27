<?php declare(strict_types=1);

use _models\Auth\User;
use _models\Auth\Password;
use Dotenv\Dotenv;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Kerwin\Core\Support\Facades\Database;
use Kerwin\Core\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

final class PasswordResetTest extends TestCase
{
    public $client;
    public $session;
    public $password;
    public $user;

    protected function setUp(): void
    {
        $this->password = new Password(); 
        $this->user = new User(); 
        $dotenv = Dotenv::createImmutable(__DIR__.'/..');
        $dotenv->load();
        $baseUri = Config::getAppAddress();
        $this->client = new Client(['base_uri' => $baseUri]);
        $this->session = new Session(new PhpBridgeSessionStorage());
    }

    public function testPasswordResetWithShortestPeriod()
    {
        $insert = [
            'token' => $this->session->get('token'),
            'name' => 'Tester',
            'email' => 'test@test.com',
            'role' => 2,
            'password' => 'password',
            'password_confirm' => 'password',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->user->create($insert);
        $forgot = $this->password->forgot(['email' => $insert['email']]);
        $user = Database::table('users')->where("email = '{$insert['email']}'")->first();
        $this->user->delete($user->id);
        $date = date('Y-m-d H:i:s', strtotime($insert['created_at'].'+1 days'));
        $this->assertStringContainsString('密碼更新時間小於一天，'.$date.'後才可以再次更改。',$forgot['msg']);
    }
}