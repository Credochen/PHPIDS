<?php

/**
 * PHP IDS
 *
 * Requirements: PHP5, SimpleXML
 *
 * Copyright (c) 2007 PHPIDS (http://php-ids.org)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the license.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @version	$Id$
 */

require_once 'PHPUnit/Framework/TestCase.php';
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/../../lib');
require_once 'IDS/Report.php';
require_once 'IDS/Event.php';
require_once 'IDS/Filter/Regex.php';

class IDS_ReportTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->report = new IDS_Report(array(
			new IDS_Event("key_a", 'val_b',
				array(
					new IDS_Filter_Regex('^test_a1$', 'desc_a1', array('tag_a1', 'tag_a2'), 1),
					new IDS_Filter_Regex('^test_a2$', 'desc_a2', array('tag_a2', 'tag_a3'), 2)
				)
			),
			new IDS_Event('key_b', 'val_b',
				array(
					new IDS_Filter_Regex('^test_b1$', 'desc_b1', array('tag_b1', 'tag_b2'), 3),
					new IDS_FIlter_Regex('^test_b2$', 'desc_b2', array('tag_b2', 'tag_b3'), 4),
				)
			)
		));
	}

	public function testEmpty()
	{
		$this->assertFalse($this->report->isEmpty());
		$report = new IDS_Report;
		$this->assertTrue($report->isEmpty());
	}

	public function testCountable()
	{
		$this->assertEquals(2, count($this->report));
	}

	public function testGetterByName()
	{
		$this->assertEquals("key_a", $this->report->getEvent("key_a")->getName());
		$this->assertEquals("key_b", $this->report->getEvent("key_b")->getName());
	}

	public function testGetTags()
	{
		$this->assertEquals(array('tag_a1', 'tag_a2', 'tag_a3', 'tag_b1', 'tag_b2', 'tag_b3'), $this->report->getTags());
	}

	public function testImpactSum()
	{
		$this->assertEquals(10, $this->report->getImpact());
	}

	public function testHasEvent()
	{
		$this->assertTrue($this->report->hasEvent('key_a'));
	}

	public function testAddingAnotherEventAfterCalculation()
	{
		$this->testImpactSum();
		$this->testGetTags();
		$this->report->addEvent(new IDS_Event('key_c', 'val_c', array(new IDS_Filter_Regex('test_c1', 'desc_c1', array('tag_c1'), 10))));
		$this->assertEquals(20, $this->report->getImpact());
		$this->assertEquals(array('tag_a1', 'tag_a2', 'tag_a3', 'tag_b1', 'tag_b2', 'tag_b3', 'tag_c1'), $this->report->getTags());
	}

	public function testIteratorAggregate()
	{
		$this->assertType('IteratorAggregate', $this->report);
		$this->assertType('IteratorAggregate', $this->report->getIterator());
	}
}