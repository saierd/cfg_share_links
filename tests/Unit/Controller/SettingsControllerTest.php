<?php

namespace OCA\CfgShareLinks\Tests\Unit\Controller;

use OCA\CfgShareLinks\Controller\SettingsController;
use OCP\AppFramework\Http;
use OCP\IConfig;
use OCP\IRequest;
use PHPUnit\Framework\TestCase;

class SettingsControllerTest extends TestCase {
	protected $config;
	protected $request;
	protected SettingsController $controller;

	protected function setUp(): void {
		$this->config = $this->getMockBuilder(IConfig::class)->getMock();
		$this->request = $this->getMockBuilder(IRequest::class)->getMock();
		$this->controller = new SettingsController($this->config, $this->request);
	}

	public function testFetch() {
		$result = $this->controller->fetch();
		$data = $result->getData();

		$this->assertEquals(Http::STATUS_OK, $result->getStatus());
		$this->assertIsArray($data);

		$this->assertArrayHasKey('defaultLabelMode', $data);
		//		$this->assertIsInt($data['defaultLabelMode']);
		//		$this->assertLessThanOrEqual(2, $data['defaultLabelMode']);
		//		$this->assertGreaterThanOrEqual(0, $data['defaultLabelMode']);

		$this->assertArrayHasKey('defaultLabel', $data);
		//		$this->assertIsString($data['defaultLabel']);

		$this->assertArrayHasKey('minTokenLength', $data);
		//		$this->assertIsInt($data['minTokenLength']);
		//		$this->assertGreaterThanOrEqual(1, $data['minTokenLength']);

		$this->assertArrayHasKey('deleteRemovedShareConflicts', $data);
		//		$this->assertIsBool($data['deleteRemovedShareConflicts']);
	}

	public function testSave() {
		for ($i = 0; $i < 5; $i++) {
			$result = $this->controller->save('default_label_mode', (string)$i);
			if ($i <= 2) {
				$this->assertEquals(Http::STATUS_OK, $result->getStatus());
			} else {
				$this->assertEquals(Http::STATUS_BAD_REQUEST, $result->getStatus());
			}

			$result = $this->controller->save('min_token_length', (string)$i);
			if ($i == 0) {
				$this->assertEquals(Http::STATUS_BAD_REQUEST, $result->getStatus());
			} else {
				$this->assertEquals(Http::STATUS_OK, $result->getStatus());
			}

			$result = $this->controller->save('deleteRemovedShareConflicts', (string)$i);
			$this->assertEquals(Http::STATUS_OK, $result->getStatus());
		}

		$result = $this->controller->save('default_label', 'Test label');
		$this->assertEquals(Http::STATUS_OK, $result->getStatus());
		$result = $this->controller->save('default_label', '');
		$this->assertEquals(Http::STATUS_BAD_REQUEST, $result->getStatus());
	}
}
