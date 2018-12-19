<?php

namespace Ntb\RestAPI;

use SS_Object;
use SapphireTest;
use SS_HTTPRequest;
use TestOnly;

class PaginationExtensionTest extends SapphireTest {

    private $controller;

    public function setUp() {
        parent::setUp();
        PaginationTestController::add_extension('Ntb\RestAPI\PaginationExtension');
        $this->controller = new PaginationTestController();
    }

    public function testDefaultLimit() {
        $this->assertEquals(20, $this->controller->getLimit(new SS_HTTPRequest("GET", "/")));
    }

    public function testDefaultOffset() {
        $this->assertEquals(0, $this->controller->getOffset(new SS_HTTPRequest("GET", "/")));
    }
}

class PaginationTestController extends SS_Object implements TestOnly {

    public function getLimit($request) {
        return $this->limit($request);
    }

    public function getOffset($request) {
        return $this->offset($request);
    }
}
