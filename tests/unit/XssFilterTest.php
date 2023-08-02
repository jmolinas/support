<?php

use Illuminate\Http\Request;
use JMolinas\Support\Http\Middleware\XSSProtection;

class XssFilterTest extends TestCase
{
    public function testXssFilter()
    {
        $string = "|asdf". chr(9) . chr(128) . "_123|";
        $request = new Request;
        $request->merge(['string' => $string]);
        $middleware = new XSSProtection;

        $middleware->handle(
            $request,
            function ($req) {
                $this->assertEquals('|asdf_123|', $req->string);
            }
        );
    }
}
