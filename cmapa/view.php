<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Prints a particular instance of cmapa
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_cmapa
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Replace cmapa with the name of your module and remove this line.

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // ... cmapa instance ID - it should be named as the first character of the module.

if ($id) {
    $cm         = get_coursemodule_from_id('cmapa', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $cmapa  = $DB->get_record('cmapa', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $cmapa  = $DB->get_record('cmapa', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cmapa->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('cmapa', $cmapa->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$event = \mod_cmapa\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $cmapa);
$event->trigger();

// Print the page header.

$PAGE->set_url('/mod/cmapa/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($cmapa->name));
$PAGE->set_heading(format_string($course->fullname));

/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_cacheable(false);
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('cmapa-'.$somevar);
 */

// Output starts here.
echo $OUTPUT->header();

// Conditions to show the intro can change to look for own settings or whatever.
if ($cmapa->intro) {
    echo $OUTPUT->box(format_module_intro('cmapa', $cmapa, $cm->id), 'generalbox mod_introbox', 'cmapaintro');
}

// Replace the following lines with you own code.
//echo $OUTPUT->heading('Yay! It works!');
$lang = $USER->lang;
$parts = explode("_",$lang);
if(count($parts)>1) {
	$lang = $parts[0] .'_'.strtoupper($parts[1]);
}
$answer = '<meta name="gwt:property" content="locale='.$lang.'">';//AQUI
$id="2";
$name="er";
$response="";
$answer .= '<link type="text/css" rel="stylesheet" href="'
		.$CFG->wwwroot.'/question/type/conceptmap/cmapweb/CmapWeb.css">'
				.'<script type="text/javascript" language="javascript" src="'
						.$CFG->wwwroot.'/question/type/conceptmap/cmapweb/cmapweb/cmapweb.nocache.js"></script>'
								.'<input type="hidden" id="'.$id
								.'" name="'.$name.'" value="'.$response.'">';//response guarda el mapa
	
	$readonly=false;
	// if corrige, $readonly=true; usar capabilitis
if($readonly) {
	$answer .= '<div id="conceptmap" style="background-color:white" width="640" height="480" readonly="true" input="'.$id.'"></div>';
} else {
	$answer .= '<div id="conceptmap" style="background-color:white" width="640" height="480" readonly="false" input="'.$id.'"></div>';
}
echo $answer;
//java_ajax_generar una guardado automatico.
//boton



// Finish the page.
echo $OUTPUT->footer();
