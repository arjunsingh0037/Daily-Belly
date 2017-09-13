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
 * Course renderer.
 *
 * @package    theme_brink
 * @author     Arjun Singh(arjunsingh@elearn10.com)
 * @copyright  2017 Dhruv Infoline Pvt Ltd
 * @license    http://lmsofindia.com
 */

namespace theme_brink\output\core;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use html_writer;
use coursecat;
use coursecat_helper;
use stdClass;
use course_in_list;

class course_renderer extends \core_course_renderer {

    /**
     * Renders the list of courses
     *
     * This is internal function, please use {@link core_course_renderer::courses_list()} or another public
     * method from outside of the class
     *
     * If list of courses is specified in $courses; the argument $chelper is only used
     * to retrieve display options and attributes, only methods get_show_courses(),
     * get_courses_display_option() and get_and_erase_attributes() are called.
     *
     * @param coursecat_helper $chelper various display options
     * @param array $courses the list of courses to display
     * @param int|null $totalcount total number of courses (affects display mode if it is AUTO or pagination if applicable),
     *     defaulted to count($courses)
     * @return string
     */
    protected function coursecat_courses(coursecat_helper $chelper, $courses, $totalcount = null) {
        global $CFG,$DB;
        //by Arjun
        $catid = optional_param('categoryid','',PARAM_INT);
        if(!$catid){
            $categories_all = $DB->get_records('course_categories');
            foreach ($categories_all as $catone) {
                $catid = $catone->id;
                break;
            }
            redirect($CFG->wwwroot.'/course/index.php?categoryid='.$catid);
        }
        //$category = coursecat::get($courses->category, IGNORE_MISSING);
       // print_object($catid);die();

        if ($totalcount === null) {
            $totalcount = count($courses);
        }

        if (!$totalcount) {
            // Courses count is cached during courses retrieval.
            return '';
        }

        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_AUTO) {
            // In 'auto' course display mode we analyse if number of courses is more or less than $CFG->courseswithsummarieslimit.
            if ($totalcount <= $CFG->courseswithsummarieslimit) {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
            } else {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COLLAPSED);
            }
        }

        // Prepare content of paging bar if it is needed.
        $paginationurl = $chelper->get_courses_display_option('paginationurl');
        $paginationallowall = $chelper->get_courses_display_option('paginationallowall');
        if ($totalcount > count($courses)) {
            // There are more results that can fit on one page.
            if ($paginationurl) {
                // The option paginationurl was specified, display pagingbar.
                $perpage = $chelper->get_courses_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_courses_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage,
                        $paginationurl->out(false, array('perpage' => $perpage)));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => 'all')),
                            get_string('showall', '', $totalcount)), array('class' => 'paging paging-showall'));
                }
            } else if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                // The option for 'View more' link was specified, display more link.
                $viewmoretext = $chelper->get_courses_display_option('viewmoretext', new \lang_string('viewmore'));
                $morelink = html_writer::tag('div', html_writer::link($viewmoreurl, $viewmoretext),
                        array('class' => 'paging paging-morelink'));
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // There are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode.
            $pagingbar = html_writer::tag(
                'div',
                html_writer::link(
                    $paginationurl->out(
                        false,
                        array('perpage' => $CFG->coursesperpage)
                    ),
                    get_string('showperpage', '', $CFG->coursesperpage)
                ),
                array('class' => 'paging paging-showperpage')
            );
        }

        // Display list of courses.
        $attributes = $chelper->get_and_erase_attributes('courses');
        $content = html_writer::start_tag('div', $attributes);

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        $coursecount = 1;
        $content .= html_writer::start_tag('div', array('class'=>'panel-group','id'=>'accordion'));
        foreach ($courses as $course) {
            //$content .= $this->coursecat_coursebox($chelper, $course, 'col-md-4');
            //by Arjun
            $content .= $this->coursebox_brink($course);
            // if ($coursecount % 3 == 0) {
            //     $content .= html_writer::end_tag('div');
            //     $content .= html_writer::start_tag('div', array('class' => 'row'));
            // }

            // $coursecount ++;
        }

        $content .= html_writer::end_tag('div');

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        if (!empty($morelink)) {
            $content .= $morelink;
        }

        $content .= html_writer::end_tag('div'); // End courses.
        return $content;
    }

    /**
     * Displays one course in the list of courses.
     *
     * This is an internal function, to display an information about just one course
     * please use {@link core_course_renderer::course_info_box()}
     *
     * @param coursecat_helper $chelper various display options
     * @param course_in_list|stdClass $course
     * @param string $additionalclasses additional classes to add to the main <div> tag (usually
     *    depend on the course position in list - first/last/even/odd)
     * @return string
     */
    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
        global $CFG;
        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        if ($course instanceof stdClass) {
            require_once($CFG->libdir. '/coursecatlib.php');
            $course = new course_in_list($course);
        }
        $content = html_writer::start_tag('div', array('class' => $additionalclasses));

        $classes = trim('card coursebox clearfix');
        if ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $nametag = 'h3';
        } else {
            $classes .= ' collapsed';
            $nametag = 'div';
        }

        // End coursebox.
        $content .= html_writer::start_tag('div', array(
            'class' => $classes,
            'data-courseid' => $course->id,
            'data-type' => self::COURSECAT_TYPE_COURSE,
        ));
        $content .= $this->coursecat_coursebox_content($chelper, $course);

        $content .= html_writer::end_tag('div'); // End coursebox.

        $content .= html_writer::end_tag('div'); // End col-md-4.

        return $content;
    }

    /**
     * Returns HTML to display course content (summary, course contacts and optionally category name)
     *
     * This method is called from coursecat_coursebox() and may be re-used in AJAX
     *
     * @param coursecat_helper $chelper various display options
     * @param stdClass|course_in_list $course
     * @return string
     */
    protected function coursecat_coursebox_content(coursecat_helper $chelper, $course) {
        global $CFG;

        if ($course instanceof stdClass) {
            require_once($CFG->libdir. '/coursecatlib.php');
            $course = new course_in_list($course);
        }

        // Course name.
        $coursename = $chelper->get_course_formatted_name($course);
        $coursenamelink = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
                                            $coursename, array('class' => $course->visible ? '' : 'dimmed'));

        $content = $this->get_course_summary_image($course);

        $content .= html_writer::start_tag('div', array('class' => 'card-block'));
        $content .= "<h4 class='card-title'>". $coursenamelink ."</h4>";

        // Display course summary.
        if ($course->has_summary()) {
            $content .= html_writer::start_tag('p', array('class' => 'card-text'));
            $content .= $chelper->get_course_formatted_summary($course,
                    array('overflowdiv' => true, 'noclean' => true, 'para' => false));
            $content .= html_writer::end_tag('p'); // End summary.
        }

        $content .= html_writer::end_tag('div');

        $content .= html_writer::start_tag('div', array('class' => 'card-block'));

        // Print enrolmenticons.
        if ($icons = enrol_get_course_info_icons($course)) {
            foreach ($icons as $pixicon) {
                $content .= $this->render($pixicon);
            }
        }

        $content .= html_writer::start_tag('div', array('class' => 'pull-right'));
        $content .= html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
                            get_string('access', 'theme_brink'), array('class' => 'card-link btn btn-default'));
        $content .= html_writer::end_tag('div'); // End pull-right.

        $content .= html_writer::end_tag('div'); // End card-block.

        // Display course contacts. See course_in_list::get_course_contacts().
        if ($course->has_course_contacts()) {
            $content .= html_writer::start_tag('ul', array('class' => 'teachers'));
            foreach ($course->get_course_contacts() as $userid => $coursecontact) {
                $name = $coursecontact['rolename'].': '.
                        html_writer::link(new moodle_url('/user/view.php',
                                array('id' => $userid, 'course' => SITEID)),
                            $coursecontact['username']);
                $content .= html_writer::tag('li', $name);
            }
            $content .= html_writer::end_tag('ul'); // End teachers.
        }

        // Display course category if necessary (for example in search results).
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT) {
            require_once($CFG->libdir. '/coursecatlib.php');
            if ($cat = coursecat::get($course->category, IGNORE_MISSING)) {
                $content .= html_writer::start_tag('div', array('class' => 'coursecat'));
                $content .= get_string('category').': '.
                        html_writer::link(new moodle_url('/course/index.php', array('categoryid' => $cat->id)),
                                $cat->get_formatted_name(), array('class' => $cat->visible ? '' : 'dimmed'));
                $content .= html_writer::end_tag('div'); // End coursecat.
            }
        }

        return $content;
    }

    protected function coursebox_brink($course) {
        global $CFG,$DB;
        //print_object($course->id);
        $content = '';
        $crsheader = '<div class="panel panel-default">
                        <div class="panel-heading coursehead">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" class="plus" data-parent="#accordion" href="#collapse'.$course->id.'"> > </a>
                                <span class="cs">Course Series :</span><span class="coursename">'.$course->fullname.'</span>
                                <a class="viewmodlink" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.get_string('viewmod','theme_brink').'<a>
                                <p class="tags">';
                                $tags = $DB->get_records('tag_instance',array('itemtype'=>'course','itemid'=>$course->id),'id,tagid');
                                foreach ($tags as $tag) {
                                    $tagname = $DB->get_record('tag',array('id'=>$tag->tagid),'id,name');
                                    $taglink = '<a target="_blank" class="tagname" href="'.$CFG->wwwroot.'/tag/index.php?tc=1&tag='.$tagname->name.'">'.ucwords($tagname->name).'</a>';
                                    $crsheader .= $taglink;
                                }
                 $crsheader .= '</p>
                            </h4>
                        </div>
                        <div id="collapse'.$course->id.'" class="panel-collapse collapse coursebody">
                            <div class="panel-body">
                                <p>Display module list here with progress</p>
                            </div>
                        </div>
                </div>';
        $content .= html_writer::div($crsheader,'courselist');

        return $content;
    }

    /**
     * Returns the first course's summary issue
     *
     * @param $course the course object
     * @return string
     */
    protected function get_course_summary_image($course) {
        global $CFG;

        $contentimage = '';
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
            if ($isimage) {
                    $contentimage = html_writer::empty_tag('img', array('src' => $url, 'alt' => 'Course Image '. $course->fullname,
                        'class' => 'card-img-top w-100'));
                    break;
            }
        }

        if (empty($contentimage)) {
            $url = $CFG->wwwroot . "/theme/brink/pix/default_course.jpg";

            $contentimage = html_writer::empty_tag('img', array('src' => $url, 'alt' => 'Course Image '. $course->fullname,
                        'class' => 'card-img-top w-100'));
        }

        return $contentimage;
    }
    // public function course_category($category) {
    //    global $OUTPUT, $CFG,$DB,$PAGE;
    //     require_once($CFG->libdir . '/coursecatlib.php');
    //     //$PAGE->set_context(context_system::instance());
    //     //$categories = coursecat::make_categories_list();
    //     //print_object($categories);
    //     //return $output;
    //     if(!empty(optional_param('categoryid','',PARAM_INT))){
    //        return parent::course_category($category); 
    //     }

    //     $content = '';
    //     $output = '';
    //     $catlist = array();
    //     $courselist = array();
    //     $categorylist = $DB->get_records_sql("Select * from {course_categories} where visible=1 and name !='Meeting'");
         
    //             foreach($categorylist as $cid => $catlist) 
    //             {
    //                 $list[]=array('id'=>$catlist->id,'name'=>$catlist->name,'count'=>$catlist->coursecount,'parent'=>$catlist->parent);
    //             }
    //             $content.='<div class="page-header">
    //                             <h1>Category Listing</h1>
    //                         </div>
    //                         <div id="ribb">
    //                             <p class="ribbon">
    //                                 <strong class="ribbon-content">
    //                                     View Course Listing
    //                                 </strong>
    //                             </p>
    //                         </div>';
    //             $count = 0;
    //             $categories2 = array();        
    //             foreach($list as $categories)
    //             {      
    //              $categories2 [] = $categories;  
    //              $count = $count + 1;   
    //              if($count == 2){
    //                 //print_object($categories2);
    //                 $content.= '<div class="row">';
    //                 foreach ($categories2 as $key => $category) {
                        
    //                     $courselist= $DB->get_records('course', array('category'=>$category['id'],'visible'=>1),'startdate DESC','id,fullname,startdate',0,100);
    //                     $content.= '<div id="coursecats" class="panel panel-default col">
    //                                    <div class="panel-heading">
    //                                        <h3 class="panel-title"><i class="fa fa-bookmark" aria-hidden="true"></i>';
    //                                        if($category['parent'] >= 1){ 
    //                                             $pid = $category['parent'];
    //                                             $parent = $DB->get_record('course_categories',array('id'=>$pid),'name');
    //                                             $parentname = $parent->name.' / ';
    //                                             $namelist = substr($parentname.''.$category['name'],0,38).'..'; 
    //                                         }else{$namelist = $category['name'];}

    //                                         $content .= '<span class="shift-left">'.$namelist.'</span>
    //                                                     <span class="shift-right">
    //                                                         <a href="'.$CFG->wwwroot.'/course/index.php?categoryid='.$category['id'].'">view all
    //                                                         </a>
    //                                                     </span>
    //                                         </h3>
    //                                     </div>
    //                                     <div class="panel-body">';
    //                                         foreach ($courselist as $course) {
    //                                             if(!empty($course->startdate)){
    //                                                 $startdate = date('d-M-Y', $course->startdate);
    //                                             }else{$startdate = '&#8213;';}
    //                                         $content .= '<span class="dates">'.$startdate.'</span> <strong>&#10072;</strong> <a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.substr($course->fullname,0,500).'</a></br>';
    //                                         }
    //                     $content.= '    </div>
    //                                 </div>'; 
    //                 }
    //                 $content.= '</div>';
    //                 unset($count);
    //                 unset($categories2);
    //              }
    //             }

    //             $output .= html_writer::div(html_writer::div($content, 'coursecatbox'), 'col-md-3 col-sm-3');
    //     return $output;
    // }
}
