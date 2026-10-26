<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginTest extends WebTestCase
{
    private const TEST_USERNAME    = 'user_tester';
    private const TEST_PASSWORD = 'SecurePass123!';

    protected function tearDown(): void
    {
        $em   = static::getContainer()->get(EntityManagerInterface::class);
        $user = $em->getRepository(User::class)->findOneBy(['username' => self::TEST_USERNAME]);
        if ($user) {
            $em->remove($user);
            $em->flush();
        }

        parent::tearDown();
    }

    /**
     * Creates and persists a test user. Must be called after createClient()
     * so the kernel is already booted.
     */
    private function createTestUser(): User
    {
        $em     = static::getContainer()->get(EntityManagerInterface::class);
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $user = new User();
        $user->setUsername(self::TEST_USERNAME);
        $user->setPassword($hasher->hashPassword($user, self::TEST_PASSWORD));
        $user->appendRole('ROLE_FREE');

        $em->persist($user);
        $em->flush();

        return $user;
    }

    public function testLoginPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testSuccessfulLogin(): void
    {
        $client = static::createClient();
        $this->createTestUser(); // kernel already booted by createClient()

        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sign in')->form([
            '_username' => self::TEST_USERNAME,
            '_password' => self::TEST_PASSWORD,
        ]);

        $client->submit($form);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testLoginFailsWithWrongPassword(): void
    {
        $client = static::createClient();
        $this->createTestUser();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Sign in')->form([
            '_username' => self::TEST_USERNAME,
            '_password' => 'WrongPassword!',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorTextContains('body', 'Invalid credentials');
    }

    public function testLoginFailsWithUnknownUsername(): void
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Sign in')->form([
            '_username' => 'nobody_example',
            '_password' => 'SomePassword123!',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorTextContains('body', 'Invalid credentials');
    }

    public function testLoginFailsWithEmptyCredentials(): void
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Sign in')->form([
            '_username' => '',
            '_password' => '',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('form');
    }
}
