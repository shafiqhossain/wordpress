<?php

/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary. In this tutorial, we are
 * going to use the WP_List_Table class directly from WordPress core.
 *
 * IMPORTANT:
 * Please note that the WP_List_Table class technically isn't an official API,
 * and it could change at some point in the distant future. Should that happen,
 * I will update this plugin with the most current techniques for your reference
 * immediately.
 *
 * If you are really worried about future compatibility, you can make a copy of
 * the WP_List_Table class (file path is shown just below) to use and distribute
 * with your plugins. If you do that, just remember to change the name of the
 * class to avoid conflicts with core.
 *
 * Since I will be keeping this tutorial up-to-date for the foreseeable future,
 * I am going to work with the copy of the class provided in WordPress core.
 */
if(!class_exists('WP_List_Table')){
    require_once(ABSPATH . 'wp-admin/includes/template.php' );
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}





/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    lakeland_bus_schedules
 * @subpackage lakeland_bus_schedules/admin
 * @author     Shafiq Hossain <md.shafiq.hossain@gmail.com>
 */
class LakeLand_Schedules_Admin extends WP_List_Table{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    public $sub_query;

    public $table_schedules;
    public $table_schedule_details;
    public $table_schedule_attributes;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version ) {
        global $status, $page, $wpdb;
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->sub_query = '';

        $this->table_schedules = $wpdb->prefix . 'schedules';
        $this->table_schedule_details = $wpdb->prefix . 'schedule_detail';
        $this->table_schedule_attributes = $wpdb->prefix . 'schedule_attributes';

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'Bus Schedule ',     //singular name of the listed records
            'plural'    => 'Bus Schedules',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in LakeLand_Tours_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The LakeLand_Tours_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/lakeland-schedules-admin.css', array(), $this->version, 'all' );


    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in LakeLand_Tours_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The LakeLand_Tours_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/lakeland-schedules-admin.js', array( 'jquery', 'jquery-ui-datepicker' ), $this->version, false );

    }



    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title()
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as
     * possible.
     *
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     *
     * For more detailed insight into how columns are handled, take a look at
     * WP_List_Table::single_row_columns()
     *
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default($item, $column_name){
        $day_list = array('', 'Monday To Friday', 'Weekend', 'Holidays');
        switch($column_name){
            case 'title_3':
                return $item[$column_name];
            case 'day':
                return $day_list[$item[$column_name]];
            case 'zone':
                return ucfirst($item[$column_name]);
            case 'zone_info':
               return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }


     /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     *
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     *
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_title($item){

        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&schedule=%s">Edit</a>',$_REQUEST['page'],'edit',$item['id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&schedule=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id']),
        );

        //Return the title contents
        return sprintf('%1$s <span style="color:silver"></span>%3$s',
            /*$1%s*/ ucfirst($item['title_3']),
            /*$2%s*/ $item['id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
    }


    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     *
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     *
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'     => 'Route',
            'day'  => 'Day',
            'zone'    => 'Direction',
            'zone_info'      => 'Title 1'
        );
        return $columns;
    }

     /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
     * you will need to register it here. This should return an array where the
     * key is the column that needs to be sortable, and the value is db column to
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     *
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     *
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('title_3',false),     //true means it's already sorted
            'day'  => array('day',false),
            'zone'    => array('zone',false),
            'zone_info'  => array('zone_info',false)
        );
        return $sortable_columns;
    }


     /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     *
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     *
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     *
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }

     /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
            $this->delete();
            //wp_die('Items deleted (or they would be if we had items to delete)!');
        }

         //Detect when a bulk action is being triggered...
        if( 'edit' === $this->current_action() ) {
            $this->render_form($_REQUEST['schedule']);
            wp_die();
        }
    }

    public function render_form( $id = 0 ) {
        global $wpdb;

        if(isset($_POST['update_schedule'])) {

            if(isset($_FILES['file'])) {
                $uploadedfile = $_FILES['file'];

                $upload_overrides = array( 'test_form' => false );

                $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

                if ( $movefile && ! isset( $movefile['error'] ) ) {

                } else {
                    /**
                     * Error generated by _wp_handle_upload()
                     * @see _wp_handle_upload() in wp-admin/includes/file.php
                     */

                }
            }

            $s_data =  array(
                    'id' => $_REQUEST['schedule'],
                    'title' => $_REQUEST['title'],
                    'day' => $_REQUEST['day'],
                    'pm_title' => $_REQUEST['pm_title'],
                    'red_title' => $_REQUEST['red_title'],
                    'zone' => $_REQUEST['zone'],
                    'zone_info' => $_REQUEST['zone_info'],
                    'title_3' => $_REQUEST['title_3'],

                    'footer_1' => $_REQUEST['footer_1'],
                    'footer_2' => $_REQUEST['footer_2'],
                    'footer_3' => $_REQUEST['footer_3'],
                    'footer_4' => $_REQUEST['footer_4'],
                    'footer_5' => $_REQUEST['footer_5'],
                    'footer_6' => $_REQUEST['footer_6'],
                    'footer_7' => $_REQUEST['footer_7'],
                    'footer_8' => $_REQUEST['footer_8'],

                    'file' => $_REQUEST['file_url']
                );
            $s_param = array(
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                );

            if(isset($movefile['url']) && $movefile['url'] != '') {
                $s_data['file'] = $movefile['url'];
            }

            $rec_status = $wpdb->replace(
                $this->table_schedules,
                $s_data,
                $s_param
            );
            $insert_id =   $wpdb->insert_id  ? $wpdb->insert_id : $_REQUEST['schedule'];

            $highlighted_values = [];
            for ($i = 0; $i < 42; $i++) {
              if (isset($_REQUEST['highlight_attr_'.$i]) && !empty($_REQUEST['highlight_attr_'.$i])) {
                $highlighted_values[$i] = 1;
              }
              else {
                  $highlighted_values[$i] = 0;
              }
            }
            $highlighted_values = json_encode($highlighted_values);

            $wpdb->replace(
             $this->table_schedule_attributes,
             array(
               'id'     => $insert_id,
               'name'   => '-highlight-',
               'values' =>  $highlighted_values
             ),
             array(
                '%d',
                '%s',
                '%s'
             )
           );

           $names = $_REQUEST['s_name'];
           $cnt = 0;

            foreach ($names as $key => $name) {
                # code...
                $s_time = json_encode($_REQUEST['s_time'][$key]);
                $wpdb->replace(
                    $this->table_schedule_details,
                    array(
                        'id'          => $_REQUEST['schedule_detail'][$key],
                        'schedule_id' => $insert_id,
                        'stop_name' => $name,
                        'schedules' =>    $s_time
                    ),
                    array(
                        '%d',
                        '%s',
                        '%s',
                        '%s'
                    )
                );

                $cnt++;
            }

            //echo $_REQUEST['schedule'];
            if($_REQUEST['schedule'] !='' && $_REQUEST['schedule'] == 0 ) {
                echo '<script>window.location.href="' . admin_url() .'admin.php?page=bus_schedule&action=edit&schedule=' . $insert_id  . '"</script>';
                exit;
            }


        }
        if($id) {
            $this->sub_query = ' where id=' . $id . ' order by id desc';
            $results = $wpdb->get_results( 'SELECT * FROM  ' . $this->table_schedules .   $this->sub_query, ARRAY_A );
            $data = $results[0];
        } else {
            $data = array(
                'title' => '',
                'day' => '',
                'zone' => '',
                'zone_info' => '',
            );
        }
        extract($data);

    ?>

        <div class="wrap">
            <h1 id="add-new-user">
            <?php if($id) {
                echo 'Update ';
            } else {
                echo 'Add ';
            }
            ?>
             Schedule</h1>
            <form name="lakeland_contact_edit"  method="post"  enctype="multipart/form-data">
            <table class="form-table">
                <tbody>
                <tr class="form-field">
                    <th scope="row"><label for="title"> Routes </label></th>
                    <td>
                        <select name="title"  id="title" >
                             <option value="Route 46 Online" <?php echo ($title == 'Route 46 Online' ? 'selected' : '') ?>>Route 46 Online</option>
                              <option value="Route 80 Online" <?php echo ($title == 'Route 80 Online' ? 'selected' : '') ?>>Route 80 Online</option>
                              <option value="Route 78 Online" <?php echo ($title == 'Route 78 Online' ? 'selected' : '') ?>>Route 78 Online</option>
                        </select>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="date">Direction </label></th>
                    <td><select name="zone"  id="zone" >
                         <option value="east" <?php echo ($zone == 'east' ? 'selected' : ''); ?>>East</option>
                          <option value="west" <?php echo ($zone == 'west' ? 'selected' : ''); ?>>West</option>
                    </select> </td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="date">Print Title </label></th>
                    <td><input type="text" name="zone_info"  id="zone_info"  value="<?php echo $zone_info; ?>"  style="width:300px"></td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="month">Operating Title</label></th>
                    <td>
                    <select name="day"  id="day" >
                         <option value="1" <?php echo ($day == '1' ? 'selected' : ''); ?>>Monday To Friday</option>
                          <option value="2"  <?php echo ($day == '2' ? 'selected' : ''); ?>>Weekends</option>
                          <option value="3" <?php echo ($day == '3' ? 'selected' : ''); ?>>Holidays</option>
                    </select>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="date">Online Title </label></th>
                    <td><input type="text" name="title_3"  id="title_3"  value="<?php echo $title_3; ?>" style="width:300px"></td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="date">PM Title</label></th>
                    <td><input type="text" name="pm_title"  id="pm_title" style="width:300px"  value="<?php echo $footer_1; ?>"></td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="date">Red Title </label></th>
                    <td><input type="text" name="red_title"  id="red_title" style="width:300px"  value="<?php echo $footer_1; ?>"></td>
                </tr>
                 <tr class="form-field">
                    <th scope="row"><label for="date"> Print File attachment </label></th>
                    <td>

                    <?php if($file) { echo '<a href="'.$file. '" target="new">' . $file . '</a>'; } ?>
                    <input type="hidden" name="file_url" value="<?php echo $file; ?>">
                    <input type="file" name="file"></td>
                </tr>

                <tr class="form-field ">
                    <td colspan="2">

                    <div  style="overflow:auto;width: 1060px"  class="stop-schedule-container" >

                    <table cellspacing="0" cellpadding="0"   style="padding:0px;margin:0px;position:relative">
                        <tr>
                            <th style="text-align:left;position: relative;left:0;z-index: 9999;background: #F1F1F1" class="lakeland-col-name ">Stops</th>
                            <th style="text-align:left" colspan="42">Timing</th>
                        </tr>

                        <?php $attributes = $wpdb->get_row(
                                $wpdb->prepare("SELECT * FROM " . $this->table_schedule_attributes . " WHERE id=%d AND name=%s", $id, '-highlight-'),
                                ARRAY_A
                              );
                        ?>
                        <?php $values = json_decode($attributes['values']); ?>
                        <tr class="stop-attributes">
                          <td style="padding: 0;margin: 0px;position: relative;left:0;z-index: 9999"  class="lakeland-col-name ">
                            <div style="width: 250px !important; margin-left: 30px !important;padding: 8px 0 8px 8px;background-color: #f1f1f1;" class="s-highlight"> Highlight Columns in RED</div>
                            <input type="hidden" name="schedule_highlight"  class="s-schedule" value="<?php echo $id ?>" />
                          </td>
                          <?php for($i = 0; $i< 42; $i++): ?>
                          <td  style="padding:0px;margin:0px;text-align: center;" class="<?php echo ($i==0 ? 'first' : '' );?> ">
                            <input type="checkbox" name="highlight_attr_<?php echo $i; ?>" class="s-highlight" style="width: auto" <?php echo isset($values[$i]) && $values[$i] == 1 ? 'checked' : '';  ?>  value="1">
                          </td>
                          <?php endfor; ?>
                        </tr>

                        <?php
                            $results = $wpdb->get_results( 'SELECT * FROM  ' . $this->table_schedule_details .  ' WHERE schedule_id=' . $id , ARRAY_A );
                            $cnt = 0;
                            foreach ($results as $result) {
                            $schedule = json_decode($result['schedules']);

                            if($result['stop_name'] != '-lbl-'){
                        ?>
                            <tr class="stop-schedule">
                              <td   style="padding: 0;margin: 0px;position: relative;left:0;z-index: 9999"  class="lakeland-col-name ">
                                <input type="text" value="<?php echo $result['stop_name'] ?>" name="s_name[<?php echo $cnt; ?>]" width="250px" style="width: 250px !important; margin-left: 30px !important" class="s-name" />
                            	<input type="hidden" name="schedule_detail[<?php echo $cnt; ?>]"  class="s-schedule" value="<?php echo $result['id'] ?>" />
                            	<span class="remove-stop-schedule" data-id="<?php echo $result['id']; ?>">X</span>
                              </td>
                              <?php for($i = 0; $i< 42; $i++): ?>
                              <td  style="padding:0px;margin:0px" class="<?php echo ($i==0 ? 'first' : '' );?> ">
                                <input type="text" name="s_time[<?php echo $cnt; ?>][]" class="s-time"  style="width: 50px"  maxlength="5" value="<?php echo $schedule[$i] ?>">
                              </td>
                              <?php endfor; ?>
                            </tr>
                            <?php } else { ?>
                                <tr class="stop-label-schedule stop-schedule">
                                     <td style="text-align:left;position: relative;left:0;z-index: 9999;background: #F1F1F1;padding:7px 0 0 50px;margin:0" class="lakeland-col-name ">
                                       <span class="remove-stop-schedule" data-id="<?php echo $result['id']; ?>">X</span> Label
                                     </td>
                                     <th style="text-align:left;padding:0;margin:0" colspan="42">
                                       <input type="hidden" name="schedule_detail[<?php echo $cnt; ?>]"  class="s-schedule" value="<?php echo $result['id'] ?>" />
                                       <input type="hidden" name="s_name[<?php echo $cnt; ?>]" class="s-label-caption" value="<?php echo $result['stop_name'] ?>"  />
                                       <input type="text"   name="s_time[<?php echo $cnt; ?>][]" class="s-label" value="<?php echo $schedule[0] ?>"/>
                                     </th>
                                </tr>
                            <?php }
                            ?>

                        <?php
                            $cnt++;
                         }
                          if(!count($results)) { ?>
                            <tr class="stop-schedule">
                              <td   style="padding: 0;margin: 0px;position: relative;left:0;z-index: 9999"  class="lakeland-col-name ">
                                <input type="text" value="Dover (Terminal)" name="s_name[1]" width="150px" style="width: 150px !important; margin-left: 30px !important" class="s-name" />
                            	<input type="hidden" name="schedule_detail[1]"  class="s-schedule" />
                            </td>

                            <?php for($i = 0; $i< 42; $i++): ?>
							<td  style="padding:0px;margin:0px" class="<?php echo ($i==0 ? 'first' : '' );?> ">
								<input type="text" name="s_time[1][]" class="s-time"  style="width: 50px" value="00:00" maxlength="5">
							</td>
                            <?php endfor; ?>

                            </tr>
                         <?php } ?>

                        </table>
                        </div>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row">
                        <a href="javascript:void(0)" class="add-stop-schedule">Add More</a>
                        <a href="javascript:void(0)" class="add-label-schedule">Add Label</a>
                    </th>
                    <td></td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="date">Footer 1 </label></th>
                    <td><input type="text" name="footer_1"  id="footer_1" style="width:300px"  value="<?php echo $footer_1; ?>"></td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="date">Footer 2 </label></th>
                    <td><input type="text" name="footer_2"  id="footer_2" style="width:300px" value="<?php echo $footer_2; ?>"></td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="date">Footer 3 </label></th>
                    <td><input type="text" name="footer_3"  id="footer_3" style="width:300px" value="<?php echo $footer_3; ?>"></td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="date">Footer 4 </label></th>
                    <td><input type="text" name="footer_4"  id="footer_4" style="width:300px" value="<?php echo $footer_4; ?>"></td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="date">Footer 5 </label></th>
                    <td><input type="text" name="footer_5"  id="footer_5" style="width:300px"  value="<?php echo $footer_5; ?>"></td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="date">Footer 6 </label></th>
                    <td><input type="text" name="footer_6"  id="footer_6" style="width:300px"  value="<?php echo $footer_6; ?>"></td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="date">Footer 7 </label></th>
                    <td><input type="text" name="footer_7"  id="footer_7"  style="width:300px" value="<?php echo $footer_7; ?>"></td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="date">Footer 8 </label></th>
                    <td><input type="text" name="footer_8"  id="footer_8" style="width:300px" value="<?php echo $footer_8; ?>"></td>
                </tr>
                </tbody>


            </table>

            <p class="submit"><input type="submit" name="update_schedule" id="update_schedule" class="button button-primary" value="Submit"></p>
            </form>
            <table class="display-none">
            <tr class="stop-label-schedule-template stop-label-schedule">
                 <td style="text-align:left;position: relative;left:0;z-index: 9999;background: #F1F1F1;padding:7px 0 0 0;margin:0" class="lakeland-col-name ">Label</td>
                 <th style="text-align:left;padding:0;margin:0" colspan="42">
                    <input type="hidden" value="" name="" class="s-label-caption" value="label" />
                     <input type="text" value="" name="s_time[]" class="s-label"/>
                 </th>
            </tr>
            </table>
        </div>
    <?php
    }

    function delete() {
        global $wpdb;
        if(isset($_REQUEST['schedule']) && $_REQUEST['schedule'] ) {
            $ids = is_array($_REQUEST['schedule']) ? implode(',', $_REQUEST['schedule']) : $_REQUEST['schedule'];
            $query = "DELETE FROM  ". $this->table_schedules ." where id in (" . $ids. ")";

            $wpdb->get_results($query);
        }
        return '';
    }

    function search_action($keyword = null) {
        $sub_query = '';
        if($keyword) {
            $sub_query = " where title like '%" . $keyword . "%'";
        }
        return $sub_query;
    }

    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     *
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 15;


        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();


        /**
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);


        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();


        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example
         * package slightly different than one you might build on your own. In
         * this example, we'll be using array manipulation to sort and paginate
         * our data. In a real-world implementation, you will probably want to
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */

        if(isset($_REQUEST['s']) && $_REQUEST['s'] != '') {
            $this->sub_query = $this->search_action($_REQUEST['s']);

        }


        $results = $wpdb->get_results( 'SELECT * FROM  ' . $this->table_schedules .   $this->sub_query, ARRAY_A );

        $data = $results;

        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         *
         * In a real-world situation involving a database, you would probably want
         * to handle sorting by passing the 'orderby' and 'order' values directly
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');


        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         *
         * In a real-world situation, this is where you would place your query.
         *
         * For information on making queries in WordPress, see this Codex entry:
         * http://codex.wordpress.org/Class_Reference/wpdb
         *
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/


        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
         */
        $current_page = $this->get_pagenum();

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $total_items = count($data);


        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);



        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;


        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

}



/** ************************ REGISTER THE TOUR PAGE ****************************
 *******************************************************************************
 * Now we just need to define an admin page. For this example, we'll add a top-level
 * menu item to the bottom of the admin menus.
 */
function schedule_management_items(){

    add_menu_page('Bus Schedule', 'Bus Schedule', 'activate_plugins', 'bus_schedule', 'bus_schedule_list_page', 'dashicons-calendar-alt', 6);

}
add_action('admin_menu', 'schedule_management_items');


/** *************************** RENDER  PAGE ********************************
 *******************************************************************************
 * This function renders the admin page and the example list table. Although it's
 * possible to call prepare_items() and display() from the constructor, there
 * are often times where you may need to include logic here between those steps,
 * so we've instead called those methods explicitly. It keeps things flexible, and
 * it's the way the list tables are used in the WordPress core.
 */
function bus_schedule_list_page(){

    //Create an instance of our package class...
    $testListTable = new LakeLand_Schedules_Admin($plugin_name, $version);
    //Fetch, prepare, sort, and filter our data...

    $testListTable->prepare_items();

    ?>


    <div class="wrap">

        <div id="icon-users" class="icon32"><br/></div>
        <h2>Schedule  <a href="admin.php?page=bus_schedule&action=edit&schedule=0"     class=" page-title-action">Add New</a></h2>

        <form method="post">
            <input type="hidden" name="page"  value="bus_schedule">
            <!-- Now we can render the completed list table -->


            <?php
            $testListTable->search_box('Search', 'search_id');
            $testListTable->display();
            ?>
        </form>

    </div>

    <?php
}


function schedule_delete() {
    global $wpdb;
    $table_schedule_details = $wpdb->prefix . 'schedule_detail';

    $results = $wpdb->get_results( 'DELETE FROM  ' . $table_schedule_details .  ' WHERE id=' . $_REQUEST['id'] , ARRAY_A );
    //echo 'DELETE FROM  ' . $table_schedule_details .  ' WHERE id=' . $_REQUEST['id'];
}
add_action('wp_ajax_schedule_delete', 'schedule_delete');
add_action('wp_ajax_nopriv_schedule_delete', 'schedule_delete');
