<?php
/* !

 * WordPress TRACKO

 */

// Exit if accessed directly

if (!defined('ABSPATH'))
    exit;

// --------------------------------------------------------------------



function tracko_component_tracko_plugin_settings() {



    tracko_admin_welcome_panel();



    $assets_setup_base_url = TRACKO_PLUGIN_URL . 'assets/img/';
    $options_woo_setting = get_option('tracko_woo_settings_data');
    $user_record_data = get_option('tracko_woo_user_status');
    $options_woo_setting = maybe_unserialize($options_woo_setting);
    ?>

    <div class="container-fluid">
        <div class="tracko-container">
            <img src="<?php echo $assets_setup_base_url . '/tracko_logo_v2.png'; ?>">
            <div class="tab-content" style="background-color: white;padding: 30px;">
                <div id="home" class="tab-pane fade in active" >
                    <div class="row" style="margin-top: -30px;">
                        <div class="div_box blue" >
                            <span class="heading">How Tracko plugin works:</span>
                        </div>
                        <div class="div_box" style="height: 260px;padding: 20px;">
                            <div style="width: calc(100% - 270px);float: left;padding:0px;">
                                <ul style="list-style-type: decimal;margin-top: 5px;">
                                    <li style="margin-top: 5px;">Install the Tracko plugin and activate it on your woo commerce dashboard.</li>
                                    <li style="margin-top: 5px;">Register your website on the plugin by filling simple information.</li>
                                    <li style="margin-top: 5px;">Tracko plugin will redirect you to your Tracko dashboard.</li>
                                    <li style="margin-top: 5px;">All the orders from your woo commerce website will be saved as tasks on your Tracko dashboard.</li>
                                    <li style="margin-top: 5px;">You can see all your orders on map on your Tracko dashboard.</li>
                                    <li style="margin-top: 5px;">You can add your delivery team to your dashboard.</li>
                                    <li style="margin-top: 5px;margin-bottom: 5px;">Assign tasks to your delivery team from the Tracko dashboard.</li>

                                </ul>
                            </div>
                            <div style="width:270px;float: left;padding: 20px; background-color: white;">
                                <table style="width: 250px;">
                                    <tr>
                                        <td><p style="font-size: 16px;font-weight: bold;">Contact Us</p></td>
                                    </tr>

                                    <tr>
                                        <td><p>Email : sales@tracko.link</p></td>
                                    </tr>
                                    <tr>
                                        <td><p>Website : <a href="http://www.tracko.link">www.tracko.link</a></p></td>
                                    </tr>
                                    <tr>
                                        <td><p>Phone : +919818122879</p></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="div_box blue">
                            <span class="heading">Configuration:</span>
                        </div>
                        <div class="div_box" >
                            <form method="post" id="tracko_wo_setup_form" name="tracko_wo_setup_form" action="">
                                <table border="0" class="form_detail">
                                    <tr>
                                        <td class="td_odd">First Name</a></td>
                                        <td style=""><input type="text" name="txtusername" class ="td_even" id="txtusername" value="<?php echo isset($options_woo_setting['first_name']) ? esc_attr($options_woo_setting['first_name']) : ''; ?>" autocomplete="off" required="required"/>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td class="td_odd">Last Name</a></td>
                                        <td style=""><input type="text" name="txtlastname" class ="td_even" id="txtlastname" value="<?php echo isset($options_woo_setting['last_name']) ? esc_attr($options_woo_setting['last_name']) : ''; ?>" autocomplete="off" required="required"/>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td class="td_odd">Email Address</a></td>
                                        <td style=""><input type="text" name="txtemail" class ="td_even" id="txtemail" value="<?php echo isset($options_woo_setting['email_id']) ? esc_attr($options_woo_setting['email_id']) : ''; ?>" autocomplete="off" required="required"/>

                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td class="td_odd">Company Name</a></td>
                                        <td style=""><input type="text" name="txtcompanyname" class ="td_even" id="txtcompanyname" value="<?php echo isset($options_woo_setting['company_name']) ? esc_attr($options_woo_setting['company_name']) : ''; ?>" autocomplete="off" required="required"/>

                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td class="td_odd">Mobile Number</a></td>
                                        <td style=""><input type="text" name="txtphone" class ="td_even" id="txtphone" value="<?php echo isset($options_woo_setting['phone_number']) ? esc_attr($options_woo_setting['phone_number']) : ''; ?>" autocomplete="off" required="required"/>

                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td class="td_odd">Password</a></td>
                                        <td style=""><input type="password" name="txtpassword" class ="td_even" id="txtpassword" value="<?php echo isset($options_woo_setting['password']) ? esc_attr($options_woo_setting['password']) : ''; ?>" autocomplete="off" required="required"/>

                                        <td ><input type="hidden" name="txtsiteurl" id="txtsiteurl" value="<?php echo get_bloginfo('url'); ?>"/></td>
                                    </tr>
                                    <tr>




                                        <?php
                                        if (empty($options_woo_setting)) {
                                            ?>
                                        <tr>
                                            <td class="td_odd"></td>
                                            <td class="td_odd"><input type="submit" value="Submit" class="cmdSubmitWoData" id="save-tracko-settings" style="margin-right: 0px !important;"/></a></td>
                                            <td class="td_odd"></td>
                                        </tr>
                                        <?php
                                    } else {
                                        ?>
                                        <tr> <td class="td_odd"></td><td class="td_odd">
                                                <div class="md-ripple-container">

                                                    <button class="visit" onclick="window.open('http://tracko.link?url=<?php echo get_bloginfo('url'); ?>&email=<?php echo isset($options_woo_setting['email_id']) ? esc_attr($options_woo_setting['email_id']) : ''; ?>', '_blank')" title="Tracko" type="button"> Go To Dashboard </button>



                                                </div>
                                            </td><td class="td_odd"></td></tr>
                                        <?php
                                    }
                                    ?>


                                    <?php wp_nonce_field('tracko_wo_setup_form'); ?>



                                    </tr>

                                </table>

                            </form>
                        </div>
                        <div class="div_box" >
                            <div class="container-fluid full-width_a ">
                                <div class="tracko-container wrk_head ">
                                    <h2>
                                        The technology to empower your sales, services and delivery workforce.

                                    </h2>

                                </div>

                                <div class="tracko-container article_a ">
                                    <div class="tracko-row ">
                                        <div class="tracko-col-md">
                                            <div class="art_img">

                                                <img src="<?php echo $assets_setup_base_url . '/2017-11-02.png'; ?>">
                                            </div>

                                            <h1>Live Tracking</h1>
                                            <p>
                                                Track all your field employees in real time on map. Filter as per location and status.
                                            </p>
                                        </div>
                                        <div class="tracko-col-md">
                                            <div class="art_img">

                                                <img src="<?php echo $assets_setup_base_url . '/t8.png'; ?>">
                                            </div>

                                            <h1>Schedule Tasks</h1>
                                            <p>
                                                Schedule tasks to all on field employees as per location/availability in real time. Upload task in bulk through excel sheets.
                                            </p>
                                        </div>
                                        <div class="tracko-col-md">
                                            <div class="art_img">

                                                <img src="<?php echo $assets_setup_base_url . '/gps.png'; ?>">
                                            </div>
                                            <h1>Navigation Support</h1>
                                            <p>
                                                Real time navigation support to on filed employees to save time on money during sales or delivery tasks.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="tracko-row ">
                                        <div class="tracko-col-md">
                                            <div class="art_img">

                                                <img src="<?php echo $assets_setup_base_url . '/Analytics-512.png'; ?>">
                                            </div>
                                            <h1>Insights & Analytics</h1>
                                            <p>
                                                Analyze your business manage daily compensation of your field agents. Plan and schedule tasks ahead and save time and money.
                                            </p>
                                        </div>
                                        <div class="tracko-col-md">
                                            <div class="art_img">

                                                <img src="<?php echo $assets_setup_base_url . '/t7.png'; ?>">
                                            </div>
                                            <h1>Real Time Reporting</h1>
                                            <p>
                                                Get reports from your field agents in real time on the move. Be aware of all your operators from a single dashboard.

                                            </p>
                                        </div>
                                        <div class="tracko-col-md">
                                            <div class="art_img">

                                                <img src="<?php echo $assets_setup_base_url . '/route plannin.png'; ?>">
                                            </div>
                                            <h1>Route Planning</h1>
                                            <p>
                                                Identify shortest routed and plan schedule for your field agents in advance. Save time and money.
                                            </p>
                                        </div>

                                    </div>


                                </div>
                            </div>

                        </div>
                    </div>

                </div>




            </div>
        </div>
    </div>

    <script>

    <?php
    if (isset($options_woo_setting['type'])) {
        ?>

            var formid = '#tracko_wo_setup_form';

            jQuery(formid + ' input').prop('disabled', true);

            jQuery(formid + ' label').css('top', '-18px');

            jQuery(formid + ' label').css('font-size', '12px');

        <?php
    }
    ?>

        keywords_list = '<?php //echo $keywords;        ?>';

    </script>

    <?php
}

tracko_component_tracko_plugin_settings();



// --------------------------------------------------------------------	

