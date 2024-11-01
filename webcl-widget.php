<?php
/*
Plugin Name: WebCL Widget
Plugin URI: http://streamcomputing.eu/blog/2013-06-05/webcl-widget-for-wordpress/
Description: WebCL Widget shows which OpenCL devices are available
Author: Vincent Hindriksen
Version: 1.1
Author URI: http://streamcomputing.eu/
*/
 
 
class WebCLWidget extends WP_Widget
{
  function WebCLWidget()
  {
    $widget_ops = array('classname' => 'WebCLWidget', 'description' => 'Shows if WebCL is detected' );
    $this->WP_Widget('WebCLWidget', 'WebCL Detection', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
    $title = $instance['title'];
    $text = $instance['text'];
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('text'); ?>">Text: <input class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" value="<?php echo attribute_escape($text); ?>" /></label></p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['text'] = $new_instance['text'];
    return $instance;
  }

  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
	$text  = empty($instance['text']) ? '<i>(WebCL is OpenCL in your browser)</i>' : apply_filters('widget_text', $instance['text']);

    if (!empty($title))
      echo $before_title . $title . $after_title;;
 
    // WIDGET CODE GOES HERE

	echo "<div id=\"webclwidget_detected\"></div>";
	echo "<div id=\"webclwidget_devices\"></div>";
	echo $text."<br/>";

//<i>(<a href="http://streamcomputing.eu/blog/tag/webcl/">WebCL</a> is OpenCL in your browser)</i>

    $code=<<<CODE
<script type="text/javascript">
// < ![CDATA[
function webclwidget_detectCL() {
  // First check if the WebCL extension is installed at all
  if (window.WebCL == undefined) {
    return "Your system does not support WebCL.";
  }

  // Get a list of available CL platforms, and another list of the
  // available devices on each platform. If there are no platforms,
  // or no available devices on any platform, then we can conclude
  // that WebCL is not available.

  try {
    var platforms = WebCL.getPlatformIDs();
    var devices = [];
    for (var i in platforms) {
      var plat = platforms[i];
      devices[i] = plat.getDeviceIDs(WebCL.CL_DEVICE_TYPE_ALL);
    }
    return "Your system supports WebCL!";
  } catch (err) {
    return "WebCL is installed, but not working correctly (error = <i>\""+err.message+"\"</i>).";
  }
}

function webclwidget_setDetectCL(elem) {
  obj = document.getElementById(elem);
  obj.innerHTML = webclwidget_detectCL();
}

function webclwidget_get_devices() {
  s = "These are your devices:<ul>";
  try {
    var platforms = WebCL.getPlatformIDs ();
    for (var i in platforms) {
      var plat = platforms[i];
      var devices = plat.getDeviceIDs (WebCL.CL_DEVICE_TYPE_ALL);
      for (var j in devices) {
        var dev = devices[j];
	devicename = dev.getDeviceInfo(WebCL.CL_DEVICE_NAME);
	if (dev.getDeviceInfo(WebCL.CL_DEVICE_VENDOR) == "Advanced Micro Devices, Inc.") {
		devicename = getRadeonModelByCodename(devicename);
	}
        s += "<li>" + devicename + "</li>";
      }
    }
  } catch (err) {
    return err.message;
  }
  return s + "</ul>";
}

function getRadeonModelByCodename(codename) {
	if ("RV790XT" == codename) { return "Radeon HD 4890"; }
	else if ("RV770XT" == codename) { return "Radeon HD 4870"; }
	else if ("RV790GT" == codename) { return "Radeon HD 4860"; }
	else if ("RV770PRO" == codename) { return "Radeon HD 4850"; }
	else if ("RV770LE" == codename) { return "Radeon HD 4830"; }
	else if ("RV740" == codename) { return "Radeon HD 4770"; }
	else if ("RV740PRO" == codename) { return "Radeon HD 4750"; }
	else if ("RV770CE" == codename) { return "Radeon HD 4730"; }
	else if ("RV730XT" == codename) { return "Radeon HD 4670"; }
	else if ("RV730PRO" == codename) { return "Radeon HD 4650"; }
	else if ("RV710" == codename) { return "Radeon HD 4350/4550/4570"; }
	else if ("Cedar" == codename) { return "Radeon HD 5450/6350/7350/8350/6330M/6350M/6370M"; }
	else if ("Redwood" == codename) { return "Radeon HD 5550/5570/5670/6530M/6550M/6570M"; }
	else if ("Juniper" == codename) { return "Radeon HD 5750/5770"; }
	else if ("Cypress" == codename) { return "Radeon HD 5830/5850/5870"; }
	else if ("Hemlock" == codename) { return "Radeon HD 5970"; }
	else if ("M92" == codename) { return "Mobility Radeon HD 530v/545v"; }
	else if ("M96" == codename) { return "Mobility Radeon HD 550v/560v/565v"; }
	else if ("M92" == codename) { return "Mobility Radeon HD 5145"; }
	else if ("M96" == codename) { return "Mobility Radeon HD 5165"; }
	else if ("Park" == codename) { return "Mobility Radeon HD 5430/5450/5470"; }
	else if ("Madison" == codename) { return "Mobility Radeon HD 5650/5750/5730/5770"; }
	else if ("Broadway" == codename) { return "Mobility Radeon HD 5830/5850/5870"; }
	else if ("Caicos" == codename) { return "Radeon HD 6450/7450/7470/8450/8470/8490/6430M/6450M/6470M/6490M"; }
	else if ("Turks" == codename) { return "Radeon HD 6570/6670/7510/7570/7670/6630M/6650M/6730M/6750M/6770M"; }
	else if ("Juniper" == codename) { return "Radeon HD 6750/6770/7510/7570/7670/6830M/6850M/6850M/6870M"; }
	else if ("Barts" == codename) { return "Radeon HD 6790/6850/6870/6950M/6970M/6990M"; }
	else if ("Cayman" == codename) { return "Radeon HD 6930/6950/6970"; }
	else if ("Antilles" == codename) { return "Radeon HD 6990"; }
	else if ("Wrestler" == codename) { return "Radeon HD 6250"; }
	else if ("Ontario" == codename) { return "Radeon HD 6290"; }
	else if ("Wrestler" == codename) { return "Radeon HD 6310"; }
	else if ("Zacate" == codename) { return "Radeon HD 6320"; }
	else if ("WinterPark" == codename) { return "Radeon HD 6370D/6410D/6380G"; }
	else if ("BeaverCreek" == codename) { return "Radeon HD 6530D/6550D/6480G/6520G/6620G"; }
	else if ("Cape Verde" == codename) { return "Radeon HD 7730/7750/7770/8760"; }
	else if ("Bonaire" == codename) { return "Radeon HD 7790/8770"; }
	else if ("Pitcairn" == codename) { return "Radeon HD 7850/7870 GHz Edition/8860"; }
	else if ("Tahiti" == codename) { return "Radeon HD 7870 XT/7950/7970/8950/8970"; }
	else if ("Malta" == codename) { return "Radeon HD 7990/8990"; }
	else if ("Seymour" == codename) { return "Radeon HD 7430M/7450M/7470M/7490M"; }
	else if ("Thames" == codename) { return "Radeon HD 7510M/7530M/7550M/7570M/7590M/7610M/7630M/7650M/7670M/7690M/7690M XT"; }
	else if ("Chelsea" == codename) { return "Radeon HD 7730M/7750M/7770M"; }
	else if ("Heathrow" == codename) { return "Radeon HD 7850M/7870M"; }
	else if ("Wimbledon" == codename) { return "Radeon HD 7970M"; }
	else if ("Scrapper" == codename) { return "Radeon HD 7420G/7520G"; }
	else if ("Devastator" == codename) { return "Radeon HD 7480D/7540D/7560D/7660D/7640G/7660G/8670D"; }
	else if ("Oland" == codename) { return "Radeon HD 8570/8670"; }
	else if ("Sun" == codename) { return "Radeon HD 8550M/8570M/8590M"; }
	else if ("Mars" == codename) { return "Radeon HD 8670M/8690M/8730M/8750M/8770M/8790M"; }
	else if ("Venus" == codename) { return "Radeon HD 8830M/8850M/8870M/8890M"; }
	else if ("Saturn" == codename) { return "Radeon HD 8930M/8950M"; }
	else if ("Neptune" == codename) { return "Radeon HD 8970M/8990M"; }
	else return "Unknown model";
}



function webclwidget_setDevices(elem) {
  obj = document.getElementById(elem);
  obj.innerHTML = webclwidget_get_devices();
}


webclwidget_setDetectCL("webclwidget_detected");
webclwidget_setDevices("webclwidget_devices");
// ]]></script><br/>
CODE;
 
	echo $code;
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("WebCLWidget");') );
?>
