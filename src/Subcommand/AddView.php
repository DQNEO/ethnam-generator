<?php
// vim: foldmethod=marker
/**
 *  AddView.php
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id$
 */
use Ethnam\Generator\Command as Ethna_Command;

// {{{ Ethna_Subcommand_AddView
/**
 *  add-view handler
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 *  @package    Ethna
 */
class Ethna_Subcommand_AddView extends Ethna_Subcommand_AddAction
{
    /**
     *  add view
     *
     *  @access public
     */
    function perform()
    {
        //
        //  '-w[with-unittest]' and '-u[unittestskel]' option
        //  are not intuisive, but I dare to define them because
        //  -t and -s option are reserved by add-[action|view] handle
        //  and Ethna_Getopt cannot interpret two-character option.
        //
        $r = $this->_getopt(
                  array('basedir=',
                        'skelfile=',
                        'with-unittest',
                        'unittestskel=',
                        'template',
                        'locale=',
                        'encoding=',
                  )
              );
        if (Ethna::isError($r)) {
            return $r;
        }
        list($opt_list, $arg_list) = $r;

        // view_name
        $view_name = array_shift($arg_list);
        if ($view_name == null) {
            return Ethna::raiseError('view name isn\'t set.', 'usage');
        }
        $r = Ethna_Controller::checkViewName($view_name);
        if (Ethna::isError($r)) {
            return $r;
        }

        // add view(invoke parent class method)
        $ret = $this->_perform('View', $view_name, $opt_list);
        if (Ethna::isError($ret) || $ret === false) { 
            return $ret;
        }

        // add template
        if (isset($opt_list['template'])) {
            $ret = $this->_performTemplate($view_name, $opt_list);
            if (Ethna::isError($ret) || $ret === false) { 
                return $ret;
            }
        }

        return true;
    }

    /**
     *  Special Function for generating template.
     *
     *  @param  string $target_name Template Name
     *  @param  array  $opt_list    Option List.
     *  @access protected
     */
    function _performTemplate($target_name, $opt_list)
    {
        // basedir
        if (isset($opt_list['basedir'])) {
            $basedir = realpath(end($opt_list['basedir']));
        } else {
            $basedir = getcwd();
        }

        // skelfile
        if (isset($opt_list['skelfile'])) {
            $skelfile = end($opt_list['skelfile']);
        } else {
            $skelfile = null;
        }

        // locale
        $ctl = Ethna_Command::getAppController(getcwd());
        if (isset($opt_list['locale'])) {
            $locale = end($opt_list['locale']);
            if (!preg_match('/^[A-Za-z_]+$/', $locale)) {
                return Ethna::raiseError("You specified locale, but invalid : $locale", 'usage');
            }
        } else {
            if (Ethna::isError($ctl)) {
                $locale = 'ja_JP';
            } else {
                $locale = $ctl->getLocale();
            }
        }

        $r = Ethna_Subcommand_Base::generate('Template', $basedir,
                                        $target_name, $skelfile, $locale);
        if (Ethna::isError($r)) {
            printf("error occurred while generating skelton. please see also following error message(s)\n\n");
            return $r;
        }

        $true = true;
        return $true;
    }

    /**
     *  get handler's description
     *
     *  @access public
     */
    function getDescription()
    {
        return <<<EOS
add new view to project:
    {$this->id} [options... ] [view name]
    [options ...] are as follows.
        [-b|--basedir=dir] [-s|--skelfile=file]
        [-w|--with-unittest] [-u|--unittestskel=file]
        [-t|--template] [-l|--locale] [-e|--encoding]
    NOTICE: "-w" and "-u" options are ignored when you specify -t option.
            "-l" and "-e" options are enabled when you specify -t option.

EOS;
    }

    /**
     *  @access public
     */
    function getUsage()
    {
        return <<<EOS
ethna {$this->id} [options... ] [view name]
    [options ...] are as follows.
        [-b|--basedir=dir] [-s|--skelfile=file]
        [-w|--with-unittest] [-u|--unittestskel=file]
        [-t|--template] [-l|--locale] [-e|--encoding]
    NOTICE: "-w" and "-u" options are ignored when you specify -t option.
            "-l" and "-e" options are enabled when you specify -t option.
EOS;
    }
}
// }}}
