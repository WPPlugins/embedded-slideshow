<?php
/*
Plugin Name: Embedded Slideshow
Plugin URI: http://sexywp.com/embedded-slideshow.htm
Description: Use short code to insert a slideshow to your post or page. The pictures or photos will come from your Picasa Albums.
Version: 0.3
Author: Charles
Author URI: http://sexywp.com/
*/

//Parse the shortcode syntax. The core function of the plugin.
//[picasa width="400" height="300" autoplay="1" showcaption="1" user="charles" album="MyPhotos" bgcolor="000000"]
function es_picasa_handler($atts){
    $defaults = array(
        'user'       => '',
        'album'      => '',
        'url'        => '',
        'width'      => '400',
        'height'     => '200',
        'autoplay'   => '0',
        'showcaption'=> '1',
        'bgcolor'    => '000000'
    );
    extract(shortcode_atts($defaults, $atts));

    $bgcolor = preg_replace('%^#%','',$bgcolor);
    if (empty($url)){
        if (empty($user) || empty($album)) return '';
        $album = preg_replace('#^es_#','',$album);
    }else{
        preg_match("@^(?:http://)?picasaweb.google.com/([\w-]+)/([\w-\.%]+)#?$@i",$url, $matches);
        if ($matches && count($matches) == 3){
            $user = $matches[1];
            $album = $matches[2];
        }else{
            return '';
        }
    }
    $noautoplay = (intval($autoplay))?'':'&amp;noautoplay=1';
    $caption = (intval($showcaption))?'&amp;captions=1':'';

    ob_start();
    ?>
<object type="application/x-shockwave-flash" data="http://picasaweb.google.com/s/c/bin/slideshow.swf" width="<?php echo $width;?>" height="<?php echo $height;?>">
<param name="movie" value="http://picasaweb.google.com/s/c/bin/slideshow.swf" />
<param name="flashvars" value="host=picasaweb.google.com<?php echo $noautoplay; echo $caption;?>&RGB=0x<?php echo $bgcolor;?>&feed=http%3A%2F%2Fpicasaweb.google.com%2Fdata%2Ffeed%2Fapi%2Fuser%2F<?php echo $user;?>%2Falbum%2F<?php echo $album;?>%3Fkind%3Dphoto%26alt%3Drss" />
<param name="pluginspage" value="http://www.macromedia.com/go/getflashplayer" />
<param name="wmode" value="transparent">
</object>
    <?php
    $slideshowcode = ob_get_contents();
    ob_end_clean();
    return $slideshowcode;
}
add_shortcode('picasa','es_picasa_handler');


add_action('media_buttons','es_add_mediabutton',20);
function es_add_mediabutton(){
    $imgsrc = WP_CONTENT_URL . '/plugins/embedded-slideshow/picasabutton.gif';
    $buttontips = __('Insert A Picasa Embedded Slideshow.');
    echo '<a href="#" id="add_slideshow" title="Insert a Picasa Slideshow"><img src="' . $imgsrc . '" alt="' . $buttontips . '" /></a>';
}

add_action('admin_footer','es_print_admin_html');
function es_print_admin_html(){
    ?>
<div id="es-wizard-window" class="jqmWindow" style="background-color:#C3D9FF;border:1px solid #6486C3;color:#333;padding:7px 7px 4px;">
    <table class="form-table" style="background-color:#FFF;padding:0.5em;">
        <tr>
            <td colspan="2"><h2 style="color:#F5951A;font-size:1.3em;margin:5px auto;display:block;">Pick an Album</h2></td>
        </tr>
        <tr>
            <td colspan="2">Your Google account ID is: <span id="es-user" style="color:#f00">xxx</span><a id="es-change-account" style="margin-left:20px;color:#00f;text-decoration:underline;cursor:pointer">Change</a></td>
        </tr>
        <tr valign="top">
            <th scope="row" style="font-weight:900;width:120px">Select an Album</th>
            <td><fieldset style="background-color:#f9f9f9;font-size:10px;">
                    <label for="es-album-url-directly"><input type="radio" name="radio" id="es-album-url-directly" value="0"/>Type in url directly.</label>
                    <span id="es-urlbox-holder"></span>
                    <br /><label for="es-album-wizard"><input type="radio" name="radio" id="es-album-wizard" value="1" checked="checked" />Use Wizard.(Recommended)</label>
            </fieldset></td>
        </tr>
        <tr valign="top">
            <th scope="row" style="font-weight:900;width:120px">Settings</th>
            <td><fieldset style="background-color:#f9f9f9;font-size:10px;">
                Width: <span id="es-slide-width" style="color:#f00">400</span>px Height:<span id="es-slide-height" style="color:#f00">300</span>px
                <br />Autoplay: <span id="es-slide-autoplay" style="color:#f00">Disabled</span> Photo Caption: <span id="es-slide-showcaption" style="color:#f00">Show</span>
                <br />Background Color: <span id="es-slide-bgcolor" style="color:#f00">#000</span>
                <br /><a id="es-change-settings" style="color:#00f;text-decoration:underline;cursor:pointer">Change</a>
                <a id="es-default-settings" style="margin-left:20px;color:#00f;text-decoration:underline;cursor:pointer">Set to Default</a>
            </fieldset></td>
        </tr>
    </table>
    <p style="display:block;text-align:center;"><input class="button" type="submit" name="es-album-pick-done" id="es-wizard-done" value="Done!"/>
    <input class="button" type="submit" name="es-album-pick-cancel" id="es-wizard-cancel" value="Cancel" /></p>
</div>
<div id="es-change-settings-window" class="jqmWindow" style="background-color:#C3D9FF;border:1px solid #6486C3;color:#333;padding:7px 7px 4px;z-index:5000">
    <table class="form-table" style="background-color:#FFF;padding:0.5em;">
      <tbody>
        <tr>
          <td colspan="2"><h2 style="color:#F5951A;font-size:1.3em;margin:5px auto;display:block;">Change the Settings</h2>
          </td>
      </tr>
            <tr valign="top">
                <th scope="row" style="width:120px"><label for="picasa-slide-width">Dimension:</label></th>
                <td><fieldset style="background-color:#f9f9f9;font-size:10px;">
                    <label for="picasa-slide-width">Width:</label><input class="small-text" type="text" value="400" id="picasa-slide-width" />px
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <label for="picasa-slide-height">Height:</label><input class="small-text" type="text" value="300" id="picasa-slide-height" />px
                    <br /><span class="setting-description">The width and height of the Slideshow, in px(pixels).</span>
                </fieldset></td>
            </tr>
            <tr valign="top">
                <th scope="row" style="width:120px"><label for="picasa-slide-bgcolor">Background Color:</label></th>
                <td><fieldset style="background-color:#f9f9f9;font-size:10px;">
                    <input type="text" value="#000000" id="picasa-slide-bgcolor" name="picasa-slide-bgcolor" size="7"/>
                    <img id="es_cp_toggle" src="<?php echo WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)).'/'; ?>color_wheel.png" />
                    <br /><span class="setting-description">Pick a color for your slideshow background, <br />using the color wheel or one of the presets.</span>
                    <div id="es_cp_wrap">
                        <div id="es_colorpicker" style="display:none"></div>
                        <?php
                        $colors = array(
                            // colors for solid menu
                                            '#616161',
                                            '#9a109d',
                                            '#3838a3',
                                            '#c91313',
                                            '#057979',
                                            '#078208',
                                            '#023b79',
                                            '#9c5654',
                        );
                        foreach($colors as $color){
                            echo '<div class="es_cp_preset" title="'.$color.'" style="background-color:'.$color.';"></div>';
                        }
                        ?>
                    </div>
                </fieldset></td>
            </tr>
            <tr valign="top">
                <th scope="row" style="width:120px"><label for="picasa-slide-autoplay">Autoplay:</label></th>
                <td><fieldset style="background-color:#f9f9f9;font-size:10px;">
                    <input type="checkbox" value="1" id="picasa-slide-autoplay" checked="checked" />Enable
                    <br /><span class="setting-description">Check the box to enable autoplay of the slideshow.</span>
                </fieldset></td>
            </tr>
            <tr valign="top">
                <th scope="row" style="width:120px"><label for="picasa-slide-showcaption">Show Picture's Caption:</label></th>
                <td><fieldset style="background-color:#f9f9f9;font-size:10px;">
                    <input type="checkbox" value="0" id="picasa-slide-showcaption" />Enable
                    <br /><span class="setting-description">Check the box to show caption of each photo, if it has one.</span>
                </fieldset></td>
            </tr>
        </tbody>
    </table>
    <p style="display:block;text-align:center;">
        <input type="button" class="button" value="Save" id="es-save-settings" />
        <input type="button" class="button jqmClose" value="Cancel" />
    </p>
</div>
<div id="es-album-pick-window" class="jqmWindow" style="background-color:#C3D9FF;border:1px solid #6486C3;color:#333;padding:7px 7px 4px;z-index:5000">
    <table class="form-table" style="background-color:#FFF;padding:0.5em;">
    <tr>
        <td><h2 style="color:#F5951A;font-size:1.3em;margin:5px auto 0;display:block;">Click an album cover to insert</h2></td>
    </tr>
    <tr>
        <td>
            <div id="es-cover-holder" style="overflow:auto">
                <img src="<?php echo WP_PLUGIN_URL, '/', plugin_basename(dirname(__FILE__)), '/loadinfo.net.gif';?>" />
            </div>
        </td>
    </tr>
    </table>
    <p style="display:block;text-align:center;"><input class="button" type="button" name="es-album-pick-done" id="es-album-pick-done" value="Done!" style="display:none" />
    <input class="button jqmClose" type="button" name="es-album-pick-cancel" id="es-album-pick-cancel" value="Cancel" /></p>
</div>
    <?php
}

add_action('admin_init','es_enqueue_scripts');
function es_enqueue_scripts(){
    wp_enqueue_script(array('sack'));
    global $wp_version;
    if (strpos($wp_vesion,'2.6') !== false || strpos($wp_version, '2.5') !== false) return;
    //make sure to include the color picker script
    wp_enqueue_script('farbtastic');
	wp_enqueue_style('farbtastic');
}

add_action('admin_head','es_print_admin_css');
function es_print_admin_css(){
    ?>
<style type="text/css">
/* jqModal base Styling courtesy of;
	Brice Burgess <bhb@iceburg.net> */

/* The Window's CSS z-index value is respected (takes priority). If none is supplied,
	the Window's z-index value will be set to 3000 by default (via jqModal.js). */
	
.jqmWindow {
    display: none;
    
    position: fixed;
    top: 17%;
    left: 50%;
    
    margin-left: -300px;
    
    overflow: auto;
    
    background-color: #EEE;
    color: #333;
    border: 1px solid black;
    padding: 12px;
}

.jqmOverlay { background-color: #000; }

#picasa-dialog{display:none;}
#es_cp_wrap {overflow:hidden;}
#es_cp_toggle {vertical-align:-2px;cursor:pointer}
.es_cp_preset {cursor:pointer;float:left;width:30px;height:30px;-moz-border-radius:30px;-webkit-border-radius:30px;margin:4px 5px 2px 5px;}
#es_colorpicker {float:left;}

#picasa-albums-list{
    width: 100%;
    overflow: auto;
    white-space: nowrap;
    height: 250px;
}

</style>
<!--[if IE 6]>
<style type="text/css">
/* Background iframe styling for IE6. Prevents ActiveX bleed-through (<select> form elements, etc.) */
* iframe.jqm {
    position:absolute;top:0;left:0;z-index:-1;
	width: expression(this.parentNode.offsetWidth+'px');
	height: expression(this.parentNode.offsetHeight+'px');
}

/* Fixed posistioning emulation for IE6
     Star selector used to hide definition from browsers other than IE6
     For valid CSS, use a conditional include instead */
* html .jqmWindow {
     position: absolute;
     top: expression((document.documentElement.scrollTop || document.body.scrollTop) + Math.round(17 * (document.documentElement.offsetHeight || document.body.clientHeight) / 100) + 'px');
}
</style>
<![endif]-->
    <?php
}

add_action('admin_footer','es_print_admin_js');
function es_print_admin_js(){
    //inport the color picker
    global $wp_version;
    if (strpos($wp_vesion,'2.6') !== false || strpos($wp_version, '2.5') !== false){
        $farbtastic = WP_CONTENT_URL . '/plugins/'.plugin_basename(dirname(__FILE__)). '/farbtastic.js';
        $farbtastic_style = WP_CONTENT_URL . '/plugins/'.plugin_basename(dirname(__FILE__)). '/farbtastic.css';
        echo '<script type="text/javascript" src="', $farbtastic, '"></script>';
        echo '<link rel="stylesheet" href="' . $farbtastic_style . '" />';
    }
    $options = get_option('embedded-slideshow');
    $default_options = array(
        'width'       => 400,
        'height'      => 300,
        'bgcolor'     => '#000000',
        'user'        => '',
        'autoplay'    => '1',
        'showcaption' => '1'
    );
    $options = wp_parse_args($options, $default_options);
    $jqModalUrl = WP_CONTENT_URL . '/plugins/'.plugin_basename(dirname(__FILE__)). '/jqModal.js';
    echo '<script type="text/javascript" src="', $jqModalUrl, '"></script>';
    ?>
<script type="text/javascript">
    var es_width = "<?php echo $options['width'];?>";
    var es_height = "<?php echo $options['height'];?>";
    var es_bgcolor = "<?php echo $options['bgcolor'];?>";
    var es_user = "<?php echo $options['username'];?>";
    var es_autoplay = "<?php echo $options['autoplay'];?>";
    var es_showcaption = "<?php echo $options['showcaption'];?>";
</script>
<script type="text/javascript">
(function($){
    //When DOM is ready create the dialogs
    $(function (){
        //Main window
        $('#es-wizard-window').jqm({
            trigger: 'a#add_slideshow',
            modal: true,
            overlay: 30,
            onShow: es_init_wizard_window
        });
        //Settings-change window
        $('#es-change-settings-window').jqm({
            trigger: 'a#es-change-settings',
            modal: true,
            overlay: 30,
            onShow: es_init_setttings_window
        });
        //Album-pick window
        $('#es-album-pick-window').jqm({
            trigger: false,
            modal: true,
            overlay : 30
        });
    });

    /**
     * Event Handler for Main Window
     */
    $('#es-change-account').click(function(){
        $(this).hide();
        $('#es-user').hide();
        $('<input type="text"/>')
            .val('').attr('id','es-google-username')
            .appendTo($(this).parent());
        $('<input type="button" class="button" value="Save" id="es-save-user-name" />')
            .appendTo($(this).parent()).click(function(){
                var username = $.trim($('#es-google-username').val());
                if (username != ''){
                    es_user = username;
                    $('#es-user').text(es_user);
                    var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );
                    mysack.methord = 'POST';
                    mysack.setVar('action', 'es_save_user_name');
                    mysack.setVar('username', $('#es-google-username').val());
                    mysack.encVar("cookie", document.cookie, false);
                    mysack.runAJAX();
                }
                $(this).parent().find('.cancel').remove();
                $(this).remove();
                $('#es-google-username').remove();
                $('#es-change-account').show();
                $('#es-user').show();
            });
        $('<input type="button" class="cancel button" value="Cancel"/>')
            .appendTo($(this).parent()).click(function(){
                $(this).parent().find('#es-save-user-name').remove();
                $(this).remove();
                $('#es-google-username').remove();
                $('#es-change-account').show();
                $('#es-user').show();
            });
    });
    var es_init_wizard_window = function(hash){
        $('#es-user').text(es_user);
        $('#es-slide-width').text(es_width);
        $('#es-slide-height').text(es_height);
        $('#es-slide-bgcolor').text(es_bgcolor);
        $('#es-slide-autoplay').text(es_autoplay==1?'Enabled':'Disabled');
        $('#es-slide-showcaption').text(es_showcaption==1?'Show':'Hide');
        hash.w.show();
    }
    $('#es-album-url-directly').click(function(){
       $('#es-urlbox-holder').append('<br /><input type="text" id="es-album-url" name="es-album-url" value="" size="30" class="normal-text"/>');
       $('#es-album-wizard').val('0');
       $(this).val('1');
    });
    $('#es-album-wizard').click(function(){
       $('#es-urlbox-holder').empty();       
       $('#es-album-url-directly').val('0');
       $(this).val('1');
    });
    //Done button click function
    $('#es-wizard-done').click(function(){
       if($('#es-album-url-directly').val() == '1'){
           es_insert_shortcode($('#es-album-url').val(),null,null);
           $('#es-wizard-window').jqmHide();
       } else {
           //Call the Album list window
           var $cover_holder = $('#es-cover-holder');
           $cover_holder.css({backgroundColor:'#000',opacity:'0.4',position:'relative',textAlign:'center',width:'421px',height:'300px'});
           $cover_holder.find('img').css({opacity:'0.4',margin:'70px auto',height:'110px',width:'110px'});
           $('#es-album-pick-window').jqmShow();
           es_ajax_load_picasa_data($('#es-user').text());
       }
    });
    //Cancel button click function
    $('#es-wizard-cancel').click(function(){
        $('#es-album-url').val('');
        $('#es-wizard-window').jqmHide();
    });

    /**
     * Event Handler for Settings-change Window
     */
    var es_init_setttings_window = function(hash){
        $('#picasa-slide-width').val(es_width);
        $('#picasa-slide-height').val(es_height);
        $('#picasa-slide-bgcolor').val(es_bgcolor).css('backgroundColor',es_bgcolor);
        $('#picasa-slide-autoplay').val(es_autoplay);
        if (es_autoplay == 1)
            $('#picasa-slide-autoplay').attr('checked','checked');
        else
            $('#picasa-slide-autoplay').removeAttr('checked');
        $('#picasa-slide-showcaption').val(es_showcaption);
        if (es_showcaption == 1)
            $('#picasa-slide-showcaption').attr('checked','checked');
        else
            $('#picasa-slide-showcaption').removeAttr('checked');
        hash.w.show();
    }
    var toggleCheckboxVal = function(){
        if ($(this).attr('value')=='0'){
            $(this).attr('value','1');
        }else{
            $(this).attr('value','0');
        }
    }
    //Checkbox click function
    $('#picasa-slide-autoplay').click(toggleCheckboxVal);
    $('#picasa-slide-showcaption').click(toggleCheckboxVal);    
    //Save the settings
    $('#es-save-settings').click(function(){
        es_width = $('#picasa-slide-width').val();
        $('#es-slide-width').text(es_width);
        es_height = $('#picasa-slide-height').val();
        $('#es-slide-height').text(es_height);
        es_bgcolor = $('#picasa-slide-bgcolor').val();
        $('#es-slide-bgcolor').text(es_bgcolor);
        es_autoplay = $('#picasa-slide-autoplay').val();
        $('#es-slide-autoplay').text(es_autoplay == '1'?'Enabled':'Disabled');
        es_showcaption = $('#picasa-slide-showcaption').val();
        $('#es-slide-showcaption').text(es_showcaption == '1'?'Show':'Hide');
        //TODO: Save this User ID to database through AJAX way
        var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php");
        mysack.methord = 'POST';
        mysack.setVar('action','es_save_settings');
        mysack.setVar('width', es_width);
        mysack.setVar('height', es_height);
        mysack.setVar('bgcolor', es_bgcolor);
        mysack.setVar('autoplay', es_autoplay);
        mysack.setVar('showcaption', es_showcaption);
        mysack.runAJAX();
        $('#es-change-settings-window').jqmHide();
    });
    
    /**
     * Event Handler for User-change Window
     */
    //Save the user name
    $('#es-save-user-name').click();
    
    /**
     * Event Handler for Album-pick Window
     */
    var waitingimg = '<img src="<?php echo WP_PLUGIN_URL, '/', plugin_basename(dirname(__FILE__)), '/loadinfo.net.gif';?>" />';
    $('#es-album-pick-cancel').click(function(){
        $('#es-album-pick-window').jqmHide();
        //Clear the cover holder
        $('#es-cover-holder').empty();
        $('#es-cover-holder').html(waitingimg);
    });
    var es_album_cover_mousemove = function(){
        $(this).css({border:'1px solid red'});
    };
    var es_album_cover_mouseout = function(){
        $(this).css({border:'1px solid #fff'});
    };
    var es_album_cover_click = function(){
        es_insert_shortcode(null,$('#es-user').text(),$(this).attr('id'));
        $('#es-album-pick-window').jqmHide();
        $('#es-wizard-window').jqmHide();
    }
    

    /**
     * Other functions
     */
    //Insert the short code.
    var es_insert_shortcode = function(url, usr, album){
        var h = '[picasa width="';
        h += $('#es-slide-width').text();
        h += '" height="';
        h += $('#es-slide-height').text();
        h += '" bgcolor="';
        h += $('#es-slide-bgcolor').text();
        h += '" autoplay="';
        h += $('#es-slide-autoplay').text() == 'Enabled'?'1':'0';
        h += '" showcaption="';
        h += $('#es-slide-showcaption').text() == 'Show'?'1':'0';
        if (url && url != ''){
            h += '" url="' + url;
        }else{
            h += '" user="' + usr;
            h += '" album="' + album;
        }
        h += '"]';
        send_to_editor(h);
    };
    
    //Require the ajax data.
    var es_ajax_load_picasa_data = function(userID) {
        if (!userID || userID == '') return;
        var dataurl = 'http://picasaweb.google.com/data/feed/api/user/' + userID +'?alt=json';
        $.ajax({
            type     : 'GET',
            url      : dataurl,
            cache    : false,
            dataType : 'jsonp',
            success  : es_ajax_data_handler,
            error    : es_ajax_error_handler,
            timeout  : 10000
        });
        //$.get(dataurl,es_ajax_data_handler);
    };

    var es_ajax_data_handler = function(data) {
        var $list_holder = $('#es-cover-holder');
        $list_holder.empty()
            .css({backgroundColor:'#fff', opacity:'1',position:'static',textAlign:'left'});
        //Add code here to process the json data
        var entries = data['feed']['entry'];

        for(i = 0; i < entries.length; i ++){
                $('<img>')
                .attr('id','es_' + entries[i]['gphoto$name']['$t'])
                .attr('title', entries[i]['title']['$t'])
                .attr('alt', entries[i]['summary']['$t'])
                .attr('src',entries[i]['media$group']['media$thumbnail'][0]['url'])
                .attr('class', 'es-album-coverpic')
                .width(115).height(115)
                .css({margin:'0 15px 10px 0',border:'1px solid #fff'})
                .mousemove(es_album_cover_mousemove)
                .mouseout(es_album_cover_mouseout)
                .click(es_album_cover_click)
                .appendTo($list_holder);
        }
    }

    var es_ajax_error_handler = function(){
        alert('Error!');
    }
})(jQuery);


//Color Picker
var f;
f = jQuery.farbtastic('#es_colorpicker', es_color_preview);
f.linkTo(jQuery('#picasa-slide-bgcolor')).setColor(jQuery('#picasa-slide-bgcolor').val());
f.linkTo(es_color_preview);
function es_color_preview(color){
    jQuery("#picasa-slide-bgcolor").css('backgroundColor',color);
    f.linkTo(jQuery('#picasa-slide-bgcolor')).setColor(color);
    f.linkTo(es_color_preview);
}
jQuery('#es_cp_toggle').click(function(){
    jQuery('#es_colorpicker').toggle(300);
});
jQuery('.es_cp_preset').click(function(){
    es_color_preview(jQuery(this).attr('title'));
});
 

album_picker_loaded = function(){
    setTimeout(function(){},1000);
    es_ajax_load_picasa_data('TangChao.ZJU');
}

</script>
    <?php
}

add_action('wp_ajax_es_save_user_name','es_save_user_name');
function es_save_user_name(){
    if(empty($_POST['username'])) {
        exit;
    }
    $options = get_option('embedded-slideshow');
    $options['username'] = $_POST['username'];
    update_option('embedded-slideshow',$options);
    die;
}

add_action('wp_ajax_es_save_settings', 'es_save_settings');
function es_save_settings(){
    $options = get_option('embedded-slideshow');
    $options['width'] = $_POST['width'];
    $options['height'] = $_POST['height'];
    $options['bgcolor'] = $_POST['bgcolor'];
    $options['autoplay'] = $_POST['autoplay'];
    $options['showcaption'] = $_POST['showcaption'];
    update_option('embedded-slideshow', $options);
    die;
}
?>