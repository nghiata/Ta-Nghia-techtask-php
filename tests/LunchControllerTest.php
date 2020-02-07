<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LunchControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function test_recipe_now()
    {
        $this->client->request('GET', '/lunch');
        $response = '{"message":"Success","data":[]}';

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($response, $this->client->getResponse()->getContent());        
    }

    public function test_recipe_2019_03_28()
    {
        $this->client->request('GET', '/lunch/2019-03-28');
        $response = '{"message":"Success","data":[]}';

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($response, $this->client->getResponse()->getContent());        
    }

    public function test_recipe_2019_03_27()
    {
        $this->client->request('GET', '/lunch/2019-03-27');
        $response = '{"message":"Success","data":{"freshest":[],"oldest":["Hotdog"]}}';

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($response, $this->client->getResponse()->getContent());        
    }

    public function test_recipe_2019_03_25()
    {
        $this->client->request('GET', '/lunch/2019-03-25');
        $response = '{"message":"Success","data":{"freshest":["Hotdog"],"oldest":[]}}';

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($response, $this->client->getResponse()->getContent());        
    }

    public function test_recipe_2019_03_13()
    {
        $this->client->request('GET', '/lunch/2019-03-13');
        $response = '{"message":"Success","data":{"freshest":["Hotdog"],"oldest":["Ham and Cheese Toastie"]}}';

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($response, $this->client->getResponse()->getContent());        
    }

    public function test_recipe_2019_03_08()
    {
        $this->client->request('GET', '/lunch/2019-03-08');
        $response = '{"message":"Success","data":{"freshest":["Ham and Cheese Toastie","Hotdog"],"oldest":[]}}';

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($response, $this->client->getResponse()->getContent());        
    }

    public function test_recipe_2019_03_07()
    {
        $this->client->request('GET', '/lunch/2019-03-07');
        $response = '{"message":"Success","data":{"freshest":["Ham and Cheese Toastie","Hotdog"],"oldest":["Salad"]}}';

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($response, $this->client->getResponse()->getContent());        
    }

    public function test_recipe_2019_03_06()
    {
        $this->client->request('GET', '/lunch/2019-03-06');
        $response = '{"message":"Success","data":{"freshest":["Ham and Cheese Toastie","Salad","Hotdog"],"oldest":[]}}';

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($response, $this->client->getResponse()->getContent());        
    }
}
