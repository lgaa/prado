<?php

class Issue216TestCase extends PradoGenericSelenium2Test
{
	function test()
	{
		$this->url('tickets/index.php?page=Issue216');
		$this->assertSourceContains('TTabPanel doesn\'t preserve active tab on callback request');

		$this->assertVisible('ctl0_Content_tab1');

		$this->byId("ctl0_Content_btn1")->click();
		$this->pause(800);

		$this->assertText("ctl0_Content_result", "Tab ActiveIndex is : 0");

		$this->byId("ctl0_Content_tab2_0")->click();
		$this->pause(800);

		$this->assertVisible('ctl0_Content_tab2');

		$this->byId("ctl0_Content_btn1")->click();
		$this->pause(800);
		$this->assertText("ctl0_Content_result", "Tab ActiveIndex is : 1");
	}
}
