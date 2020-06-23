<?php

namespace TNO\EssifLab\Tests\Views;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Tests\Stubs\Model;
use TNO\EssifLab\Tests\TestCase;
use TNO\EssifLab\Views\ImmutableField;

class ImmutableFieldTest extends TestCase {
    /** @test */
    function does_render_input() {
        $subject = new ImmutableField($this->integration, $this->createModelWithImmutableField(true));

        $actual = $subject->render();
        $expect = '/<input.*\/>/';

        $this->assertRegExp($expect, $actual);
    }

    /** @test */
    function does_render_with_name_attr() {
        $subject = new ImmutableField($this->integration, $this->createModelWithImmutableField(true));

        $actual = $subject->render();
        $expect = '/name="namespace\[immutable]"/';

        $this->assertRegExp($expect, $actual);
    }

    /** @test */
    function does_render_with_checked_value() {
        $attrs['checked'] = true;
        $subject = new ImmutableField($this->integration, $this->createModelWithImmutableField(true));

        $actual = $subject->render();
        $expect = '/checked="1"/';

        $this->assertRegExp($expect, $actual);
    }

    /** @test */
    function does_render_without_checked_value() {
        $attrs['checked'] = false;
        $subject = new ImmutableField($this->integration, $this->createModelWithImmutableField(false));

        $actual = $subject->render();
        $expect = '/checked="1"/';

        $this->assertNotRegExp($expect, $actual);
    }

    private function createModelWithImmutableField(bool $immutable): Model
    {
        return new Model([
            Constants::TYPE_INSTANCE_DESCRIPTION_ATTR => json_encode([
                Constants::FIELD_TYPE_IMMUTABLE => $immutable,
            ]),
        ]);
    }
}
