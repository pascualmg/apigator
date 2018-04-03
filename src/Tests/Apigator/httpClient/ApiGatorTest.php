<?php
/**
 * Created by PhpStorm.
 * User: Pascual
 * Date: 01/04/2018
 * Time: 16:38
 */

namespace Apigator\httpClient;

use Apigator\Exception\NullUriApigatorException;
use PHPUnit_Framework_TestCase;


class ApiGatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function GivenApigatorWhenCallWithNullUriThenException()
    {
        $this->setExpectedException(NullUriApigatorException::class);
        $apigator = new ApiGator();
        $apigator->procesaResponseCon();
    }

    /**
     * @test
     */
    public function GivenApigatorWhenSetNullUriThenException()
    {
        $this->setExpectedException(NullUriApigatorException::class);
        $apigator = new ApiGator();
        $apigator->setUri(null);
    }

    /**
     * @test
     */
    public function GivenApigatorWhenSetUriThenIsSetted()
    {
        $expected = 'http://uriInventada.com';
        $actual = (new ApiGator($expected))->getUri();
        $this->assertEquals($expected, $actual);
    }



}
