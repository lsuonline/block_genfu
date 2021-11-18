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
 * @package    block_genfu
 * @copyright  2021 onwards LSU Online & Continuing Education
 * @copyright  2021 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Inlcude the requisite helpers functionality.
require_once($CFG->dirroot . '/blocks/genfu/classes/helpers.php');

class block_genfu extends block_list {
    public $user;
    public $content;
    public $systemcontext;

    public function init() {
        global $CFG, $PAGE;

        $this->title = get_string('pluginname', 'block_genfu');
        $this->set_user();
        $this->set_context();
    }

    /**
     * Returns the user object
     *
     * @return @object
     */
    public function set_user() {
        global $USER;
        $this->user = $USER;
    }

    /**
     * Sets and returns this course's context
     *
     * @return @context
     */
    private function set_context() {
        $this->systemcontext = context_system::instance();
    }

    /**
     * Indicates which pages types this block may be added to
     *
     * @return @array
     */
    public function applicable_formats() {
        return array(
             'site-index' => true
        );
    }

    /**
     * Standard moodle function
     *
     * @return false (only allow one instance per page)
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * Indicates that this block has its own configuration settings
     *
     * @return @bool
     */
    public function has_config() {
        return true;
    }

    /**
     * Sets the content to be rendered when displaying this block
     *
     * @return @object
     */
    public function get_content() {
        global $USER;

        if (!empty($this->content)) {
            return $this->content;
        }

        $this->content = $this->get_new_content_container();

        if (isloggedin() && has_capability('block/genfu:admin', $this->systemcontext, $USER->id)) {

            $this->add_item_to_content([
                'lang_key' => 'Settings'
            ]);

            $this->add_item_to_content([
                'lang_key' => 'Paths'
            ]);

        }

        if (isloggedin() && block_genfu_helpers::isuploader($USER)) {
            $this->add_item_to_content([
                'lang_key' => 'Upload'
            ]);
        }

        return $this->content;
    }

    /**
     * Builds and adds an item to the content container for the given params
     *
     * @param  @array $params
     */
    private function add_item_to_content($params) {
        if (!array_key_exists('query_string', $params)) {
            $params['query_string'] = [];
        }

        // Build the item.
        $item = $this->build_item($params);

//        if (block_genfu_helpers::alloweduser_check($params = array('user_id' => $this->user->id))) {
            $this->content->items[] = $item;
//        }
    }

    /**
     * Builds a content item (link) for the given params
     *
     * @param  @array $params
     * @return @string
     */
    private function build_item($params) {
        global $OUTPUT;

        // Set the label from the params.
        $label = $params['lang_key'];

        // Set the icon if we use one (which we don't but JIC), if not, blank.
        $icon = isset($params['icon_key']) ? $icon = $OUTPUT->pix_icon($params['icon_key'], $label, 'moodle', ['class' => 'icon']) : null;

        // If we're using any attrributes, populate them.
        $attrs = isset($params['attributes']) ? $params['attributes'] : null;

        // We're using spans here.
        $tag = 'span';

        // If this item is a link to a specific page.
        if (isset($params['page'])) {
            // Build the item.
            $item = html_writer::link(
                        new moodle_url('/blocks/genfu/' . $params['page'] . '.php', $params['query_string']),
                        $icon . $label, $attrs
            );
        } else {
            // Build the item.
            $item = html_writer::tag(
                    $tag,
                    $icon . $label,
                    $attrs
                );
         }

         // return the item.
         return $item;
    }

    /**
     * Returns an empty "block list" content container to be filled with content
     *
     * @return @object
     */
    private function get_new_content_container() {
        $content = new stdClass;
        $content->items = [];
        $content->icons = [];
        $content->footer = '';

        return $content;
    }
}
