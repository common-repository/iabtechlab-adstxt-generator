<?php
    /**
     * 
     *Plugin Name: Iabtechlab Adstxt Generator and Validator
     *Description: Increase transparency in the programmatic advertising ecosystem by creating ads.txt
     *Version: 2.0
     *Author: <a href="mailto:shreyanshgoel9@gmail.com">Shreyansh Goel</a>, <a href="https://vbidmanager.com">vBidManager</a>
    */

    add_option('i_adstxt_content');
    add_action('admin_init', 'i_adstxt_register_my_setting');

    function i_adstxt_register_my_setting(){
      register_setting('i_adstxt_options', 'i_adstxt_content');
    }
    function i_adstxt_custom_admin_menu() {
        add_options_page(
            'iabtechlab-Adstxt-Generator',
            'iabtechlab-Adstxt-Generator',
            'manage_options',
            'iabtechlab-adstxt-generator',
            'i_adstxt_options_page'
        );
    }

    function i_adstxt_redirect()
    {
        $currentUrl = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $humanUrl = str_replace(
            array('http://', 'https://'),
            '',
            home_url('ads.txt')
        );

        if ($humanUrl == $currentUrl) {

            header('Content-Type: text/html; charset=utf-8');
            echo html_entity_decode(get_option('i_adstxt_content'));
            die;
        }
    }

    i_adstxt_redirect();

    function i_adstxt_options_page() {
        $flag = 0;
        if($_POST['i_adstxt_create'] && wp_verify_nonce($_POST['i_adstxt_nonce'], 'i_adstxt')){
            $i=0;
            $string = '';
            $name = $_POST['i_adstxt_name'];
            foreach ($name as $key => $value) {
                $value = sanitize_text_field($value);
            }
            $comm = $_POST['i_adstxt_comm'];
            foreach ($comm as $key => $value) {
                $value = sanitize_text_field($value);
            }

            $pubid = $_POST['i_adstxt_pubid'];
            foreach ($pubid as $key => $value) {
                $value = sanitize_text_field($value);
            }
            $rel = $_POST['i_adstxt_rel'];
            foreach ($rel as $key => $value) {
                $value = sanitize_text_field($value);
            }
            $cid = $_POST['i_adstxt_cid'];
            foreach ($cid as $key => $value) {
                $value = sanitize_text_field($value);
            }
            $email = sanitize_text_field($_POST['i_adstxt_email']);
            $url = sanitize_text_field($_POST['i_adstxt_url']);
            $sub = $_POST['i_adstxt_sub'];
            foreach ($sub as $key => $value) {
                $value = sanitize_text_field($value);
            }
            if(!empty($name[0])){
                foreach ($name as $key => $value) {
                    if ($rel[$i] == 1) {
                        $temp = "DIRECT";
                    }else{
                        $temp = "RESELLER";
                    }
                    $string = $string . $name[$i] . ", " . $pubid[$i] . ', ' . $temp . ', ' . $cid[$i];
                    if ($_POST['i_adstxt_comm'][$i]) {
                        $string = $string . " #" . $comm[$i];
                    }
                    $string = $string . "<br>";
                    $i++;
                }
                if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $string = $string . "contact=" . $email . "<br>";
                }
                if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
                    $string = $string . "contact=" . $url . "<br>";
                }
                $i = 0;
                if(!empty($sub[0])){
                    foreach ($sub as $key => $value) {
                        $string = $string . "SubDomain=" . $sub[$i] . "<br>";
                        $i++;
                    }
                }
                update_option('i_adstxt_content', htmlentities($string));
                $flag = 1;
            }
        }
        $myfile = html_entity_decode(get_option('i_adstxt_content'));
        $first = [];$second = [];$third = [];$comms = [];
        if($myfile){
            $content = explode("<br>", $myfile);
            foreach ($content as $key => $value) {
                $temp = explode("#", $value,2);
                if ($temp[1]) {
                    array_push($comms, $temp[1]);
                }
                $temp = explode(",", $temp[0]);
                if(count($temp) !=4){
                    $temp = explode("=", $value);
                    if (strtolower($temp[0]) == "contact") {
                        array_push($second, $temp[1]);
                    }
                    if (strtolower($temp[0]) == "subdomain") {
                        array_push($third, $temp[1]);
                    }

                }else{
                    array_push($first, $temp);
                }
            }
        }
		wp_enqueue_style( 'bootstrap', plugins_url( '/bootstrap.min.css', __FILE__ ));
        wp_enqueue_script( 'bootstrap', plugins_url( '/bootstrap.min.js', __FILE__ ), array( 'jquery') );
?>
    <h2>Iabtechlab Adstxt Generator</h2>
    <hr>
    <!-- Latest compiled and minified CSS -->
    <!-- Latest compiled and minified JavaScript -->
    <script type="text/javascript">
        var i = 1;

        function i_adstxt_append() {
            $("#i_adstxt_container").append('<div class="col-sm-12" style="margin-top: 15px" id="i_adstxt_div' + i + '"><div class="col-sm-2"><input type="text" class="form-control" name="i_adstxt_name[]" value="" placeholder="Enter Domain Name" style="width:155px" required></div><div class="col-sm-2"><input type="text" class="form-control" name="i_adstxt_pubid[]" value="" placeholder="Enter Publisher’s Account ID" style="width:180px" required></div><div class="col-sm-2" style="margin-left: 30px;"><select style="width: 180px" name="i_adstxt_rel[]"><option value="1">DIRECT</option><option value="2">RESELLER</option></select></div><div class="col-sm-2" style="margin-left: 55px;"><input type="text" class="form-control" name="i_adstxt_cid[]" value="" placeholder="Enter Certification Authority ID" style="width:180px"></div><div class="col-sm-2" style="margin-left: 50px"><input type="text" class="form-control" name="i_adstxt_comm[]" value="" placeholder="Enter Comments" style="width:180px"></div><div style="position:absolute;margin-left:1150px;"><button type="button" onclick="i_adstxt_div_remove(' + i + ')" style="color:red" class="btn btn-link">Delete</button></div></div>');
            i++;
        }

        function i_adstxt_div_remove(j) {
            $('#i_adstxt_div' + j).remove();
        }

        var m = 1;

        function i_adstxt_sub_append() {
            $("#i_adstxt_container_sub").append('<div class="col-sm-12" id="i_adstxt_sub_div' + m + '" style="margin-top: 15px"><div class="col-sm-2"><input type="text" class="form-control" name="i_adstxt_sub[]" value="" placeholder="Enter SubDomain" style="width:155px"></div><div class="col-sm-1"><button type="button" onclick="i_adstxt_sub_div_remove(' + m + ')" style="position:absolute;color:red" class="btn btn-link">Delete</button></div></div>');
            m++;
        }

        function i_adstxt_sub_div_remove(j) {
            $('#i_adstxt_sub_div' + j).remove();
    }
    </script>
    <div class="wrap container-fluid">
        <div>
            <?php if ($flag) {?>
               <div class="alert alert-success alert-dismissable">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Success!</strong> Ads.txt is updated.
                </div> 
            <?php }?>
            <form method="post">
                <?php wp_nonce_field('i_adstxt', 'i_adstxt_nonce');?>
                <br>
                <div class="col-sm-12">
                    <div class="col-sm-2">
                        Domain Name
                        <sup style="color: red">*</sup>
                    </div>
                    <div class="col-sm-2">
                        Publisher’s Account ID
                        <sup style="color: red">*</sup>
                    </div>
                    <div class="col-sm-2" style="width:220px; margin-left: 30px;">
                        Type of Account/Relationship
                        <sup style="color: red">*</sup>
                    </div>
                    <div class="col-sm-2" style="width:245px; margin-left: 20px">
                        Certification Authority ID (optional)
                    </div>
                    <div class="col-sm-2" style="margin-left: 30px;">
                        Comments (optional)
                    </div>
                </div>
                <?php if (!empty($first)) {$i = -1; $j = 0;
                        foreach ($first as $key => $value) {?>
                <div class="col-sm-12" style="margin-top: 15px" id="i_adstxt_div<?php echo $i;?>">
                    <div class="col-sm-2">
                        <input type="text" class="form-control" name="i_adstxt_name[]" value="<?php echo $value[0]?>" placeholder="Enter Domain Name" style="width:155px" required>
                    </div>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" name="i_adstxt_pubid[]" value="<?php echo trim($value[1])?>" placeholder="Enter Publisher’s Account ID" style="width:180px" required>
                    </div>
                    <div class="col-sm-2" style="margin-left: 30px;">
                        <select style="width: 180px" name="i_adstxt_rel[]">
                            <option value="1" <?php if(trim($value[2])=='DIRECT' ) echo "selected";?>>DIRECT</option>
                            <option value="2" <?php if(trim($value[2])=='RESELLER' ) echo "selected";?>>RESELLER</option>
                        </select>
                    </div>
                    <div class="col-sm-2" style="margin-left: 55px;">
                        <input type="text" class="form-control" name="i_adstxt_cid[]" value="<?php echo trim($value[3])?>" placeholder="Enter Certification Authority ID" style="width:180px">
                    </div>
                    <div class="col-sm-2" style="margin-left: 50px">
                        <input type="text" class="form-control" name="i_adstxt_comm[]" value="<?php echo $comms[$j++];?>" placeholder="Enter Comments" style="width:180px">
                    </div>
                    <div style="position:absolute;margin-left:1150px;">
                        <button type="button" onclick="i_adstxt_div_remove(<?php echo $i;?>)" style="color:red" class="btn btn-link">Delete</button>
                    </div>
                </div>
                <?php } $i--;}else{ ?>
                <div class="col-sm-12" style="margin-top: 15px">
                    <div class="col-sm-2">
                        <input type="text" class="form-control" name="i_adstxt_name[]" value="" placeholder="Enter Domain Name" style="width:155px" required>
                    </div>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" name="i_adstxt_pubid[]" value="" placeholder="Enter Publisher’s Account ID" style="width:180px" required>
                    </div>
                    <div class="col-sm-2" style="margin-left: 30px;">
                        <select style="width: 180px" name="i_adstxt_rel[]">
                            <option value="1">DIRECT</option>
                            <option value="2">RESELLER</option>
                        </select>
                    </div>
                    <div class="col-sm-2" style="margin-left: 55px;">
                        <input type="text" class="form-control" name="i_adstxt_cid[]" value="" placeholder="Enter Certification Authority ID" style="width:180px">
                    </div>
                    <div class="col-sm-2" style="margin-left: 50px">
                        <input type="text" class="form-control" name="i_adstxt_comm[]" value="" placeholder="Enter Comments" style="width:180px">
                    </div>
                </div>
                <?php } ?>
                <div id="i_adstxt_container"></div>
                <div class="col-sm-12" style="margin-top: 15px;">
                    <button type="button" class="button-primary" onclick="i_adstxt_append()" style="float: right">Add Item</button>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-2">
                        <b>Contact (optional)</b>
                    </div>
                </div>
                <div class="col-sm-12" style="margin-top: 15px">
                    <div class="col-sm-2">
                        Email:
                    </div>
                    <div class="col-sm-4">
                        <input type="email" class="form-control" name="i_adstxt_email" value="<?php if(!empty($second[0])) echo $second[0]?>" placeholder="Enter Email" style="width:250px">
                    </div>
                </div>
                <div class="col-sm-12" style="margin-top: 15px">
                    <div class="col-sm-2">
                        Contact us URL:
                    </div>
                    <div class="col-sm-4">
                        <input type="url" class="form-control" name="i_adstxt_url" value="<?php if(!empty($second[1])) echo $second[1]?>" placeholder="Enter url" style="width:250px">
                    </div>
                </div>
                <div class="col-sm-12" style="margin-top: 20px">
                    <div class="col-sm-2">
                        <b>Subdomain (optional)</b>
                    </div>
                </div>
                <?php if (!empty($third)) {$i = -1; 
                                        foreach ($third as $key => $value) {?>
                <div class="col-sm-12" style="margin-top: 15px" id="i_adstxt_sub_div<?php echo $i;?>">
                    <div class="col-sm-2">
                        <input type="text" class="form-control" name="i_adstxt_sub[]" value="<?php echo $value;?>" placeholder="Enter SubDomain" style="width:155px">
                    </div>
                    <div class="col-sm-1">
                        <button type="button" onclick="i_adstxt_sub_div_remove(<?php echo $i; ?>)" style="position:absolute;color:red" class="btn btn-link">Delete</button>
                    </div>
                </div>
                <?php } $i--;}else{ ?>
                <div class="col-sm-12" style="margin-top: 15px">
                    <div class="col-sm-2">
                        <input type="text" class="form-control" name="i_adstxt_sub[]" value="" placeholder="Enter SubDomain" style="width:155px">
                    </div>
                </div>
                <?php }?>
                <div id="i_adstxt_container_sub"></div>
                <div class="col-sm-12" style="margin-top: 15px;">
                    <div class="col-sm-3" style="margin-top: 15px;"></div>
                    <div class="col-sm-3" style="margin-top: 15px;">
                        <button type="button" class="button-primary" onclick="i_adstxt_sub_append()">Add Item</button>
                    </div>
                </div>
                <br>
                <br>
                <button type="submit" class="btn btn-success" name="i_adstxt_create" value="Create">Create</button>
            </form>
        </div>
        <br><br>
        <div class="col-sm-12">
            <span style="color:red">Validate</span> Your Ads.txt from here <a href="https://adstxt.adnxs.com/">Click Here</a>
        </div>
    </div>
    <?php
    }
    add_action( 'admin_menu', 'i_adstxt_custom_admin_menu' );
    function i_adstxt_plugin_row_meta( $links, $file ) {
        if (strpos( $file,'iabtechlab-adstxt-generator.php') !== false ) {
            $new_links = array('<a href="mailto:shreyanshgoel9@gmail.com">Support</a>');
            $links = array_merge( $links, $new_links );
        }        
        return $links;
    }

    add_filter('plugin_row_meta', 'i_adstxt_plugin_row_meta', 10, 2 );
    add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'i_adstxt_add_action_links' );

    function i_adstxt_add_action_links ( $links ) {
        $mylinks = array(
            '<a href="options-general.php?page=iabtechlab-adstxt-generator">Settings</a>',
        );
    return array_merge( $links, $mylinks );
}