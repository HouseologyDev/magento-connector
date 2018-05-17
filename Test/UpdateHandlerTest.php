<?php

namespace B2bapp\UpdateHandler\Test;

include(__DIR__.'/../../../magento/magento2-base/dev/tests/api-functional/framework/Magento/TestFramework/TestCase/WebapiAbstract.php');

class UpdateHandlerTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    public function testBasicRoutingExplicitPath()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/b2bapp/updatedentities',
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_GET,
            ]
        ];
        $item = $this->_webApiCall($serviceInfo);
        $this->assertEquals('[]', $item, "Invalid response from updatedentities API");
    }
}