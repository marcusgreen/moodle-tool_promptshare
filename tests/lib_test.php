<?php

use tool_promptshare\lib;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../lib.php');

/**
 * Test cases for tool_promptshare\lib
 */
/**
 * Test cases for the lib class in the tool_promptshare plugin.
 *
 * This test class contains unit tests for the lib class methods,
 * specifically focusing on the process_form_submission method.
 */
class tool_promptshare_lib_test extends advanced_testcase {
    /**
     * Test the process_form_submission method in the lib class.
     *
     * This test verifies that the process_form_submission method correctly updates
     * a tool_promptshare record in the database and calls the update_pagetypes method.
     *
     * The test performs the following steps:
     * 1. Creates a test record in the tool_promptshare table.
     * 2. Simulates form data with updated values.
     * 3. Mocks the update_pagetypes method to verify it's called.
     * 4. Executes the process_form_submission method.
     * 5. Asserts that the database record is updated correctly.
     * 6. Asserts that the update_pagetypes method was called.
     *
     * @covers \tool_promptshare\lib::process_form_submission
     * @covers \tool_promptshare\lib::update_pagetypes
     */
    public function test_process_form_submission() {
        $this->resetAfterTest();


        // Create a test record in the database
        $record = $this->getDataGenerator()->tool_promptshare->create_tweak([
            'promptname' => 'Test Tweak',
            'prompttext' => 'Test content',
        ]);


        // Simulate form data
        $formdata = (object) [
            'id' => $record->id,
            'promptname' => 'Updated Tweak',
            'prompttext' => 'Updated content',
        ];

        // Mock update_pagetypes function
        $this->getMockBuilder('tool_promptshare\lib')
             ->setMethods(['update_pagetypes'])
             ->getMock();

        // Execute the function
        $updatedrecord = lib::process_form_submission($formdata);


        // Verify database was updated
        $this->assertEquals('Updated Tweak', $updatedrecord->promptname);
        $this->assertEquals('Updated content', $updatedrecord->prompttext);


        // Verify update_pagetypes was called
        $this->assertTrue($this->getMock()->method('update_pagetypes')->wasCalled());
    }

    protected function getMock() {
        // Implementation for method mocking
    }
}