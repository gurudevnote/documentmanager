<?php

namespace FOSUserOverrideBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function LoginFaith()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        //$crawler->
        $form = $crawler->selectButton('_submit')->form();

        // set some values
        $form['_username'] = 'user_not_exit';
        $form['_password'] = '123456';

        // submit the form
        $client->submit($form);
        $this->assertRegExp('/\/login$/', $client->getResponse()->headers->get('location'));

        //$client->request('GET', '/login');
        $crawler = $client->followRedirect();
        $this->assertContains(
            "Invalid credentials",
            $crawler->html()
        );
    }


    public function testAccountIsDisabled()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        //$crawler->
        $form = $crawler->selectButton('_submit')->form();

        // set some values
        $form['_username'] = 'disable';
        $form['_password'] = '123456';

        // submit the form
        $client->submit($form);
        $this->assertRegExp('/\/login$/', $client->getResponse()->headers->get('location'));

        $crawler = $client->followRedirect();
        $this->assertContains(
            "Account is disabled",
            $crawler->html()
        );
    }

    public function testLoginSuccessful()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        //$crawler->
        $form = $crawler->selectButton('_submit')->form();

        // set some values
        $form['_username'] = 'admin';
        $form['_password'] = '123456';

        // submit the form
        $client->submit($form);
        $this->assertEquals(true, $client->getResponse()->isRedirection());
        $this->assertRegExp('/\/\/localhost\/$/', $client->getResponse()->headers->get('location'));
        $crawler = $client->followRedirect();
        $this->assertContains(
            '<a href="/profile/edit">Edit profile</a>',
            $crawler->html()
        );
    }
}
