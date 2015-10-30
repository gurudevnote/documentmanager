<?php
namespace FOSUserOverrideBundle\Tests;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
class RegisterUserTest  extends WebTestCase
{
    public function testRegisterSuccessful()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register/');
        //$crawler->
        $form = $crawler->selectButton('fos_user_registration_form[submit]')->form();

        // set some values
        $form['fos_user_registration_form[email]'] = 'register_user@dev.dev';
        $form['fos_user_registration_form[username]'] = 'register_user';
        $form['fos_user_registration_form[address]'] = 'address';
        $form['fos_user_registration_form[plainPassword][first]'] = '123456';
        $form['fos_user_registration_form[plainPassword][second]'] = '123456';

        // submit the form
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertContains(
            "The user has been created successfully",
            $crawler->html()
        );

        $this->assertContains(
            "register_user",
            $crawler->html()
        );
    }

    public function testRegisterFaith()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register/');
        //$crawler->
        $form = $crawler->selectButton('fos_user_registration_form[submit]')->form();

        // set some values
        //$form['fos_user_registration_form[email]'] = 'register_user@dev.dev';
        $form['fos_user_registration_form[username]'] = 'register_user1';
        $form['fos_user_registration_form[address]'] = 'address';
        $form['fos_user_registration_form[plainPassword][first]'] = '123456';
        $form['fos_user_registration_form[plainPassword][second]'] = '123456';

        // submit the form
        $crawler = $client->submit($form);

        $this->assertContains(
            "Please enter an email",
            $crawler->html()
        );

        $crawler = $client->request('GET', '/register/');
        //$crawler->
        $form = $crawler->selectButton('fos_user_registration_form[submit]')->form();

        // set some values
        $form['fos_user_registration_form[email]'] = 'register_user';
        $form['fos_user_registration_form[username]'] = 'register_user1';
        $form['fos_user_registration_form[address]'] = 'address';
        $form['fos_user_registration_form[plainPassword][first]'] = '123456';
        $form['fos_user_registration_form[plainPassword][second]'] = '123456';

        // submit the form
        $crawler = $client->submit($form);

        $this->assertContains(
            "The email is not valid",
            $crawler->html()
        );
    }
}