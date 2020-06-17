<?php

namespace TNO\EssifLab\Tests\Views;

use TNO\EssifLab\Tests\TestCase;
use TNO\EssifLab\Views\ImmutableField;

class ImmutableFieldTest extends TestCase {
    /** @test */
    function does_render_input() {
        $attrs['checked'] = true;
        $subject = new ImmutableField($this->integration, $this->model, $attrs);

        $actual = $subject->render();
        $expect = '/<input.*\/>/';
        var_dump($actual);

        $this->assertRegExp($expect, $actual);
    }

    /** @test */
    function does_render_with_name_attr() {
        $attrs['checked'] = true;
        $subject = new ImmutableField($this->integration, $this->model, $attrs);

        $actual = $subject->render();
        $expect = '/name="namespace\[immutable]"/';

        $this->assertRegExp($expect, $actual);
    }

    /** @test */
    function does_render_with_checked_value() {
        $attrs['checked'] = true;
        $subject = new ImmutableField($this->integration, $this->model, $attrs);

        $actual = $subject->render();
        $expect = '/checked="1"/';

        $this->assertRegExp($expect, $actual);
    }

    /** @test */
    function does_render_without_checked_value() {
        $attrs['checked'] = false;
        $subject = new ImmutableField($this->integration, $this->model, $attrs);

        $actual = $subject->render();
        $expect = '/checked="1"/';

        $this->assertNotRegExp($expect, $actual);
    }
}
