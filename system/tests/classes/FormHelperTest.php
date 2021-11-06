<?php

namespace system\tests\classes;

use PHPUnit\Framework\TestCase;
use system\classes\FormHelper;

/**
 * @covers FormHelper
 */
class FormHelperTest extends TestCase
{

    private string $expected_start;
    private string $expected_end;

    public function setUp(): void
    {
        $this->expected_start = "<input type='hidden' name='_formName' value='test_form' /><input type='text' name='login' class='login_input' />";
        $this->expected_end = "<input type='submit' name='submit_form' value='Send' /></form>";
    }

    public function testSimpleForm()
    {
        $expected = "<form method='POST'>" . $this->expected_start . $this->expected_end;
        $this->expectOutputString($expected);
        FormHelper::addInput('text', 'login', ['class' => 'login_input']);
        FormHelper::addSubmit('submit_form', 'Send');
        FormHelper::createForm('test_form');
    }

    public function testSimpleFormWithFileField()
    {
        $expected = "<form method='POST' enctype='multipart/form-data'>" . $this->expected_start . "<input type='file' name='avatar' />" . $this->expected_end;
        $this->expectOutputString($expected);
        FormHelper::addInput('text', 'login', ['class' => 'login_input']);
        FormHelper::addInput('file', 'avatar');
        FormHelper::addSubmit('submit_form', 'Send');
        FormHelper::createForm('test_form');
    }

}