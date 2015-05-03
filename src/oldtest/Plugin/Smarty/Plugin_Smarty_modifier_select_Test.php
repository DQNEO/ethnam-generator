<?php
/**
 *  Plugin_Smarty_modifier_select_Test.php
 *
 *  @author     Yoshinari Takaoka <takaoka@beatcraft.com>
 */

require_once ETHNA_BASE . '/src/Plugin/Smarty/modifier.select.php';

//{{{    Ethna_Plugin_Smarty_modifier_select_Test.php
/**
 *  Test Case For modifier.select.php
 *
 *  @access public
 */
class Ethna_Plugin_Smarty_modifier_select_Test extends Ethna_UnitTestBase
{
    // {{{ test_smarty_modifier_select
    function test_smarty_modifier_select()
    {
        $r = smarty_modifier_select('a', 'b');
        $this->assertNull($r);

        $r = smarty_modifier_select('a', 'a');
        $this->assertEqual($r, 'selected="selected"');
    }
    // }}}
}
// }}}

