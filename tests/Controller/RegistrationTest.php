<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationTest extends WebTestCase
{
    protected function tearDown(): void
    {

        $em = static::getContainer()->get(EntityManagerInterface::class);

        foreach (['newuser_example', 'duplicate_example'] as $username) {
            $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);
            if ($user) {
                $em->remove($user);
                $em->flush();
            }
        }

        parent::tearDown();
    }

    public function testRegistrationPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testSuccessfulRegistration(): void
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Register')->form([
            'registration_form[username]'        => 'newuser_example',
            'registration_form[plainPassword]' => 'SecurePass123!',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/');


        $em   = static::getContainer()->get(EntityManagerInterface::class);
        $user = $em->getRepository(User::class)->findOneBy(['username' => 'newuser_example']);

        $this->assertNotNull($user, 'User should exist in the database after registration');
        $this->assertSame('newuser_example', $user->getUsername());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testRegistrationFailsWithoutEmail(): void
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Register')->form([
            'registration_form[username]'        => '',
            'registration_form[plainPassword]' => 'SecurePass123!',
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorExists('form');
        $this->assertSelectorTextContains('body', 'Please enter a username');
    }

    public function testRegistrationFailsWithoutPassword(): void
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Register')->form([
            'registration_form[username]'        => 'newuser_example',
            'registration_form[plainPassword]' => '',
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorExists('form');
        $this->assertSelectorTextContains('body', 'Please enter a password');
    }

    public function testRegistrationFailsWithDuplicateUsername(): void
    {
        $client = static::createClient();

        // Safe to access container now that createClient() has booted the kernel
        $em       = static::getContainer()->get(EntityManagerInterface::class);
        $existing = new User();
        $existing->setUsername('duplicate_example');
        $existing->setPassword('hashed-password');
        $em->persist($existing);
        $em->flush();

        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Register')->form([
            'registration_form[username]'        => 'duplicate_example',
            'registration_form[plainPassword]' => 'SecurePass123!',
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('body', 'There is already an account with this username');
    }
}
