<?php

namespace B2bapp\UpdateHandler\Test;

include(__DIR__.'/../../../magento/magento2-base/dev/tests/api-functional/framework/Magento/TestFramework/TestCase/WebapiAbstract.php');

class UpdateHandlerTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    public function testUpdatedEntitiesApi()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/b2bapp/updatedentities?entity=product',
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_GET,
            ]
        ];

        $item = $this->_webApiCall($serviceInfo);
        $this->assertEquals('[]', $item, "Invalid response from updatedentities API");
    }

    public function testSetStatusApi()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/b2bapp/setstatus',
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_POST,
                'contentType' => 'application/json'
            ]
        ];

        $requestData = ['entity' => 'product','entity_ids' => ["0" => 0]]; //Assuming that there will not be any entry with id '0'
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(0, $item, "Invalid response from setstatus API");
    }
}