<?php 
require_once("../includes/restriction.php");
ob_start(); 
if (!isset($_SESSION)) { session_start(); }
?>
<?php require_once('../../Connections/conn_database.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysqli_real_escape_string") ? mysqli_real_escape_string(conn_database(),$theValue) : mysqli_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}


$query_rs_lang = "SELECT * FROM languages WHERE status = 1 ORDER BY rank DESC";
$rs_lang = mysqli_query(conn_database(),$query_rs_lang) or die(mysql_error());
$row_rs_lang = mysqli_fetch_assoc($rs_lang);
$totalRows_rs_lang = mysqli_num_rows($rs_lang);

$colname_rs_insert = "-1";
if (isset($_GET['id'])) {
  $colname_rs_insert = $_GET['id'];
}

$query_rs_insert = sprintf("SELECT * FROM service WHERE id = %s", GetSQLValueString($colname_rs_insert, "int"));
$rs_insert = mysqli_query(conn_database(),$query_rs_insert) or die(mysql_error());
$row_rs_insert = mysqli_fetch_assoc($rs_insert);
$totalRows_rs_insert = mysqli_num_rows($rs_insert);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "service")) {

    
    $query_rs_insert = "SELECT rank FROM service ORDER BY rank DESC LIMIT 1";
    $rs_insert = mysqli_query(conn_database(),$query_rs_insert) or die(mysql_error());
    $row_rs_insert = mysqli_fetch_assoc($rs_insert);

    $new_rank = $row_rs_insert['rank']+1;

    $insertSQL = sprintf("INSERT INTO service (meta_title, meta_keywords, meta_description, meta_slug, icon, showhome, related, status, rank) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['meta_title'], "text"),
                       GetSQLValueString($_POST['meta_keywords'], "text"),
                       GetSQLValueString($_POST['meta_description'], "text"),
                       GetSQLValueString($_POST['meta_slug'], "text"),
                        GetSQLValueString($_POST['icon'], "text"),
                        GetSQLValueString(isset($_POST['showhome']) ? "true" : "", "defined","1","0"),
                        GetSQLValueString(isset($_POST['related']) ? "true" : "", "defined","1","0"),
                        GetSQLValueString(isset($_POST['status']) ? "true" : "", "defined","1","0"),
                        GetSQLValueString($new_rank , "int"));

    
    $Result1 = mysqli_query(conn_database(),$insertSQL) or die(mysql_error());

    $service_id = mysql_insert_id();

    mysqli_data_seek($rs_lang, 0);
    while ($row_rs_lang = mysqli_fetch_assoc($rs_lang)){
        $insertSQL = sprintf("INSERT INTO service_localization (title1, shortdetail1, detail1, service_id, lang_id) VALUES(%s, %s, %s, %d, %d) ",
                        GetSQLValueString($_POST['title1_'.strtolower($row_rs_lang['lang_abbr'])],'text'),
                        GetSQLValueString($_POST['shortdetail1_'.strtolower($row_rs_lang['lang_abbr'])],'text'),
                        GetSQLValueString($_POST['detail1_'.strtolower($row_rs_lang['lang_abbr'])],'text'),
                        GetSQLValueString($service_id, "int"),
                        GetSQLValueString($row_rs_lang['id'], "int"));

        
        $Result1 = mysqli_query(conn_database(),$insertSQL) or die(mysql_error());
    }

    if(isset($_POST['insert_more']))
        $insertGoTo = "insert.php";
    else
        $insertGoTo = "index.php";
    if (isset($_SERVER['QUERY_STRING'])) {
        $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
        $insertGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $insertGoTo));
}

?>
<!DOCTYPE html>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.6
Version: 4.5.4
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
        <title>WOW III : Administrator</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="../assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="../assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
        <link href="../assets/global/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-minicolors/jquery.minicolors.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="../assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="../assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="../assets/global/plugins/jcrop/css/jquery.Jcrop.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="../assets/pages/css/image-crop.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL STYLES -->
        <link href="../assets/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/typeahead/typeahead.css" rel="stylesheet" type="text/css" />
        <link rel="icon" href="../../images/favicon.png" type="image/png" />
    </head>
    <!-- END HEAD -->

    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
        <!-- BEGIN HEADER -->
        <?php include('../includes/header.php'); ?>
        <!-- END HEADER -->
        <!-- BEGIN HEADER & CONTENT DIVIDER -->
        <div class="clearfix"> </div>
        <!-- END HEADER & CONTENT DIVIDER -->
        <!-- BEGIN CONTAINER -->
        <div class="page-container">
            <!-- BEGIN SIDEBAR -->
            <div class="page-sidebar-wrapper">
                <!-- BEGIN SIDEBAR -->
                <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                <div class="page-sidebar navbar-collapse collapse">
                    <!-- BEGIN SIDEBAR MENU -->
                    <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
                    <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
                    <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
                    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                    <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
                    <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                    <?php include('../includes/left.php'); ?>
                    <!-- END SIDEBAR MENU -->
                    <!-- END SIDEBAR MENU -->
                </div>
                <!-- END SIDEBAR -->
            </div>
            <!-- END SIDEBAR -->
            <!-- BEGIN CONTENT -->
            <div class="page-content-wrapper">
                <!-- BEGIN CONTENT BODY -->
                <div class="page-content">
                    <!-- BEGIN PAGE HEADER-->
                    <!-- BEGIN THEME PANEL -->
                    
                    <!-- END THEME PANEL -->
                    <!-- BEGIN PAGE BAR --><!-- END PAGE BAR -->
                    <!-- BEGIN PAGE TITLE-->
                    
                    <!-- END PAGE TITLE-->
                    <!-- END PAGE HEADER-->

                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <span class="caption-subject uppercase">Service Insert</span>
                            </div>
                            <div class="actions">
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <div class="form-body" style="padding-bottom: 0px;">
                                <form action="<?php echo $editFormAction; ?>" name="service" id="service" method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="light">
                                                <div class="portlet-body">
                                                    <ul class="nav nav-tabs">
                                                        <li class="tab <?php if(!isset($_POST["current_tab"]) || $_POST["current_tab"] == "tab_1_1"){echo 'active';}?>">
                                                            <a onclick="setDefaultTab('tab_1_1');" href="#tab_1_1" data-toggle="tab" aria-expanded="true"> Main</a>
                                                        </li>
                                                        <?php
                                                        mysqli_data_seek($rs_lang, 0);
                                                        while ($row_rs_lang = mysqli_fetch_assoc($rs_lang)){ ?>
                                                        <li class="tab <?php if(isset($_POST["current_tab"]) && $_POST["current_tab"] == "tab_1_".$row_rs_lang['lang_abbr']){echo 'active';}?>">
                                                            <a onclick="setDefaultTab('tab_1_<?php echo $row_rs_lang['lang_abbr']; ?>');" href="#tab_1_<?php echo $row_rs_lang['lang_abbr']; ?>" data-toggle="tab" aria-expanded="true"> <?php echo $row_rs_lang['lang_name']; ?></a>
                                                        </li>
                                                        <?php } ?>
                                                    </ul>
                                                    <div class="tab-content">

                                                        <div class="tab-pane fade active in" id="tab_1_1">

                                                            <div class="form-group">
                                                                <label>Meta Title</label>
                                                                <i style="padding-left: 5px;" class="fa fa-question-circle tooltips" data-original-title="Meta Title: Meta Title is used to inform search engines and visitors what any given page on your site is about in the most concise and accurate way possible. This title will then appear in various places around the web, including the tab in your web browser (50-60 characters in length)"></i>
                                                                <input type="text" name="meta_title" id="meta_title" class="form-control" placeholder=""> 
                                                            </div>
                                                                
                                                            <div class="form-group">
                                                                <label>Meta Keywords</label>
                                                                <i style="padding-left: 5px;" class="fa fa-question-circle tooltips" data-original-title="Meta Keywords: Meta Keywords mean the keywords which are relevance and appeared to the web pages in order to keep record and build awareness for search engines (enginesven though Google has informed that Keyword Tags will not be calculated) (15 – 25 Keywords)"></i>
                                                                <input type="text" name="meta_keywords" data-role="tagsinput" id="meta_keywords" class="form-control"> 
                                                            </div>
                                                            
                                                            <div class="form-group"  >
                                                                <label>Meta Description</label>
                                                                <i style="padding-left: 5px;" class="fa fa-question-circle tooltips" data-original-title="Meta Description: Meta Description Tags, while not only important to search engine rankings, but also are extremely important in gaining user click-through from SERPs. These short paragraphs are a webmaster’s opportunity to advertise content to searchers and to let them know exactly whether the given page contains the information they're looking for. (150-160 characters in length)"></i>
                                                                <input type="text" name="meta_description" id="meta_description" class="form-control" placeholder=""> 
                                                            </div>

                                                            <hr style="margin-top: 35px;">

                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">                                          
                                                                        <label>Icon Image</label>
                                                                        <a href="../../TH/icon-list.php" target="_blank" style="font-size:11px;">icon code</a> <span style="font-size:14px; color:#F00;">*</span>
                                                                        <div class="input-group">
                                                                            <span class="input-group-addon"><i class="fa fa-info"></i></span>
                                                                            <input type="text" name="icon" id="icon" class="form-control" placeholder="" require>
                                                                        </div> 
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Slug (Page URL)</label> <span style="font-size:11px; color:#F00;">All lowercase letter and no blank space</span>
                                                                        <div class="input-group">
                                                                            <span class="input-group-addon"><i class="fa fa-link"></i></span>
                                                                            <input type="text" name="meta_slug" id="meta_slug" class="form-control" placeholder="" onChange="javascript:this.value=this.value.toLowerCase();">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label>Show Home</label>
                                                                        <div class="bootstrap-switch-container">
                                                                        <input type="checkbox" name="showhome" id="showhome" checked class="make-switch" data-size="normal" data-on-text="<i class='fa fa-check'></i>" data-off-text="<i class='fa fa-times'></i>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label>Related</label>
                                                                        <div class="bootstrap-switch-container">
                                                                        <input type="checkbox" name="related" id="related" checked class="make-switch" data-size="normal" data-on-text="<i class='fa fa-check'></i>" data-off-text="<i class='fa fa-times'></i>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label>Status</label>
                                                                        <div class="bootstrap-switch-container">
                                                                        <input type="checkbox" name="status" id="status" checked class="make-switch" data-size="normal" data-on-text="<i class='fa fa-check'></i>" data-off-text="<i class='fa fa-times'></i>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <?php mysqli_data_seek($rs_lang, 0);
                                                        while ($row_rs_lang = mysqli_fetch_assoc($rs_lang)){ ?>
                                                        <div class="tab-pane fade" id="tab_1_<?php echo $row_rs_lang['lang_abbr']; ?>">
                                                         
                                                            <div class="form-group">
                                                                <label>Service Title</label> <span style="font-size:14px; color:#F00;">*</span>
                                                                <input type="text" name="title1_<?php echo strtolower($row_rs_lang['lang_abbr']); ?>" id="title1_<?php echo strtolower($row_rs_lang['lang_abbr']); ?>" require class="form-control" placeholder=""> 
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Service Shortdetail</label>
                                                                <textarea class="form-control ckeditor" name="shortdetail1_<?php echo strtolower($row_rs_lang['lang_abbr']); ?>" id="shortdetail1_<?php echo strtolower($row_rs_lang['lang_abbr']); ?>" cols="45" rows="5"></textarea>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Service Detail</label>
                                                                <textarea class="form-control ckeditor" name="detail1_<?php echo strtolower($row_rs_lang['lang_abbr']); ?>" id="detail1_<?php echo strtolower($row_rs_lang['lang_abbr']); ?>" cols="45" rows="5"></textarea>
                                                            </div>
                                                                
                                                        </div>
                                                        <?php } ?>

                                                        <div class="clearfix margin-bottom-20"> </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                  
                                    <div class="form-actions right">
                                        <a href="index.php" class="btn dark btn-outline">Back</a>
                                        <button type="submit" name="Insert" class="btn blue btn-outline">Insert</button>
                                        <a href="#" class="btn btn-outline yellow" onclick="javascript:insert_more(this);">Insert More</a>
                                    </div>
                                    <input type="hidden" name="MM_insert" value="service">
                                </form>
                                <script>
                                function setDefaultTab(id)
                                {
                                  $("#current_tab").val(id);
                                }
                                </script> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                    </div>
                    
                </div>
                <!-- END CONTENT BODY -->
            </div>
            <!-- END CONTENT -->
            <!-- BEGIN QUICK SIDEBAR -->
            
            <!-- END QUICK SIDEBAR -->
        </div>
        <!-- END CONTAINER -->
        <!-- BEGIN FOOTER -->
        <?php include('../includes/footer.php'); ?>
        <!-- END FOOTER -->
        <!--[if lt IE 9]>
<script src="../assets/global/plugins/respond.min.js"></script>
<script src="../assets/global/plugins/excanvas.min.js"></script> 
<![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
        <script type="text/javascript" src="../assets/global/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
        <script src="../ckeditor/ckeditor.js"></script>
        <script src="../ckfinder/ckfinder.js"></script>
        <script src="../assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-minicolors/jquery.minicolors.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/components-color-pickers.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="../assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
        <script src="../assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
        <script src="../assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/components-bootstrap-tagsinput.min.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="../assets/global/plugins/jcrop/js/jquery.Jcrop.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="../assets/pages/scripts/form-image-crop.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
        <script type="text/javascript">
            function insert_more(bt){
                $($(bt).parents("form")).append($('<input type="hidden">').prop('name',"insert_more").val('insert_more')).submit();
            }

            $("#meta_slug").on('blur',checkSlug);
            $("#service").on('submit',checkSlug);
            function checkSlug(evnt){
                if($('#meta_slug').prop('value')===''){
                    if(evnt.type=='submit')
                    alert('Slug (Page URL) can not empty can not empty');
                    return false;
                }
                $("#meta_slug").val($("#meta_slug").val().replace(" ", "_"));
                var resultCheck = $.ajax({
                    type: 'POST',
                    url: '../checkSlug.php',
                    data: {tableName:'service',dataValue:$('#meta_slug').prop('value'),cID:'0'},
                    success: function( data ) {
                        if(data=='CanUse'){
                            $("#meta_slug").parents('div.form-group').find('span:first').css({'font-size':'13px','color':'greenyellow'}).text('available');
                        }else{
                            $("#meta_slug").parents('div.form-group').find('span:first').css({'font-size':'13px','color':'red'}).text('not avilable');
                        }
                        return data;
                    },
                    async:false
                });
                if(resultCheck.readyState===4){
                    if(resultCheck.responseText=="CanUse"){
                        return true;
                    }
                    if(evnt.type=='submit'){
                        alert('URL not avilable');
                        $('#meta_slug').focus();
                    }
                    return false;
                }else{
                    alert('Error Slug Checker');
                    return false;
                }
            }

            $('#service').on('submit', function(){
                var reposn = true;
                $('#service').find('input[require]').each(function(){
                    if($(this).val() == ""){
                        //alert($(this).attr('name'));
                        $("div.active.in").removeClass('active in');
                        $(this).parents('.tab-pane.fade').addClass('active in');
                        $("li.tab.active").removeClass('active');
                        // console.log($(this).parents('.tab-pane.fade').attr('id'));
                        // $('a[href="#tab_1_TH"]').addClass('active')
                        $('a[href="#'+$(this).parents('.tab-pane.fade').attr('id')+'"]').parents('li').addClass('active');
                        $(this).css("border", "red solid 3px");
                        reposn = false;
                        return false;
                    }
                });
                return reposn;
            });

        </script>
    </body>

</html>
<?php

?>