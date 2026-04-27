<?php
/*
Plugin Name: LakeLand Bus Stops
Plugin URI: http://www.isoftbd.com
Description: Pluging to render stops
Version: 3.0.0.0
Author: Shafiq Hossain (md.shafiq.hossain@gmail.com)
Author URI: http://www.isoftbd.com
*/


register_activation_hook( __FILE__, 'activate_lakeland_users' );
function activate_lakeland_users() {

}



/**
 * Register the stylesheets for the public-facing side of the site.
 *
 * @since    1.0.0
 */
function lakeland_stops_shortcode($atts, $content = null) {
 ?>
    <script type="text/javascript">
      jQuery(document).ready(function(){
        jQuery('.lakeland-more-info').on('click', function(e) {
          url = jQuery(this).attr('href');
          e.preventDefault();
          jQuery.ajax({
            type: "GET",
            url: "/wp-admin/admin-ajax.php",
            dataType: 'html',
            beforeSend: function() {
            },
            data:
              {
                action: 'user_click',
                id: jQuery(this).attr('data-id'),
                type : jQuery(this).attr('data-type')
              }
            ,
            success: function(data){

               window.location.href = url;
            }

          });
        })
          });
    </script>
   <?php

   $args = array(
    'fields'       => 'all',
    'who'          => '',
    'meta_query'   => $meta_query,
    'orderby'      => 'meta_value',
    'order'        => 'ASC',
    'meta_key'     =>  $order_by
   );




$form .=<<<HTML
<div id="stops"  data-role="page"  data-url="stops" tabindex="0" style="min-height: 1078px;">
<script type="text/javascript" src="http://www.novasoftware.com/Download/jQuery_FixedTable/jquery.fixedtable.js"></script>
  <link rel="stylesheet" href="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css"/>
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
<style>
   .ui-mobile [data-role="page"], .ui-mobile [data-role="dialog"], .ui-page {
    position: relative;
   }
  #llb-scroll-wrapper {

    width:100%;
    overflow-x:scroll;


  }
  .stops-header {

    background:#f9f9f9;
  }
    #llb-scroll {

      width: 100%;
      /** min-width:400px; **/
      overflow: visible !important;
      z-index: 999;
    }

    #llb-scroll ul {

      z-index: 1;



    }

    #llb-scroll ul {
      list-style: none;
      padding: 0;
      margin: 0;
      height: 100%;
      text-align: center;
      margin: 0px;
      padding: 0px;
    }

    #llb-scroll li {
      display: block;
      height: 100%;
      font-size: 14px;
      list-style: none;
      margin: 0px;
      padding: 0px;
    }

    h6 {

    margin:0px;
    padding:0px;
    }
    .stops-header {

    }
    th, td { white-space: normal; }
    div.llb-scroll {

    }

  .movie-list thead th,
.movie-list tbody tr:last-child {
    border-bottom: 1px solid #d6d6d6; /* non-RGBA fallback */
    border-bottom: 1px solid rgba(0,0,0,.1);
}
.movie-list tbody th,
.movie-list tbody td {
    border-bottom: 1px solid #e6e6e6; /* non-RGBA fallback  */
    border-bottom: 1px solid rgba(0,0,0,.05);
}
.movie-list tbody tr:last-child th,
.movie-list tbody tr:last-child td {
    border-bottom: 0;
}
.movie-list tbody tr:nth-child(odd) td,
.movie-list tbody tr:nth-child(odd) th {
    background-color: #eeeeee; /* non-RGBA fallback  */
    background-color: rgba(0,0,0,.04);
}
#llb-table-id td:nth-child(3) {
    text-align: center;
}
.ui-overlay-a, .ui-page-theme-a, .ui-page-theme-a .ui-panel-wrapper {
  text-shadow: none;
}
.color-w {
  color: #fff;
}
  </style>

  <section data-role="content" class="scroller"  >
    <div class="stops-header">
    <div  style="text-align: center;color:#000088">  <a title="BUS STOPS LAKE ROUTE 46 LOCAL" href="/m/bus_stops_46.php" >Route 46</a> | <a title="Bus-stops-lakeland-route-80" href="/m/bus_stops_80.php" >Route 80</a> |  <a title="Bus-stops-lakeland-route-78" class="ui-btn-active ui-state-persist" href="/m/stops.php">Route 78</a>   &nbsp;</div>
    <h5  style="text-align: center;color:#000088"><span >LAKELAND RT 78 BERNARDSVILLE TO PABT</span></h5>
    <p style="text-align: center;"><strong><em><span class="xl67250872">LAKELAND </span><span class="xl67250871">BUS STOPS ON A LINE</span></em></strong></p>
      <form style="margin-bottom:15px;">
        <input id="filterTable-input" data-type="search">
      </form>
</div>
    <div  align="center">

      <div id="llb-scroll-wrapper" class="table_scroller_llb_container">
        <div id="llb-scroll">
          <ul>
            <li>
              <div>
                <table id="llb-table-id" data-role="table" style="" data-filter="true" data-input="#filterTable-input" style="border-collapse: collapse; " border="0" cellspacing="0" cellpadding="2" data-mode="" class="llb-table ui-responsive table-stroke movie-list">

                  <thead>
                    <tr>
                      <td style="background-color: #253d87;"><span class="style6 style3 color-w"><strong>EASTBOUND</strong></span></td>
                      <td style="background-color: #253d87; " scope="col"><span class="style6 style3 color-w"><strong>BERNARDSVILLE TO NEW YORK</strong></span></td>
                      <td style="text-align: center; background-color: #253d87;" scope="colgroup"></td>
                    </tr>

                    <tr>
                      <td style="background-color: #f1f5f8;"><span style="color: #003366;"><strong><span>ON STREET</span></strong></span></td>
                      <td style="background-color: #f1f5f8;"><span style="color: #003366;"><strong><span>AT STREET</span></strong></span></td>
                      <td style="background-color: #f1f5f8; text-align: center;" scope="col"><span style="color: #003366;"><strong><span>DIR</span></strong></span></td>
                    </tr>
                  </thead>

                  <tbody>



                    <tr>

                      <td><span>Mine Brook Rd (202 N.)</span></td>
                      <td><span>(in front of)
                          BERNARDSVILLE TRAIN STA</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>RT 202 N.</span></td>
                      <td><span>ACROSS FROM KINGS</span></td>
                      <td><span>N</span></td>
                    </tr>

                    <tr>
                      <td><span>RT 202 N.</span></td>
                      <td><span>N. FINLEY AVE</span></td>
                      <td><span>N</span></td>
                    </tr>

                    <tr>
                      <td><span>N. FINLEY AVE.</span></td>
                      <td><span>RIDGE ST.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>S. FINLEY AVE</span></td>
                      <td><span>W. HENRY ST</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>S. FINLEY AVE.</span></td>
                      <td><span>CROSS ROAD (NJT RR P &amp; R)</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>STONEHOUSE ROAD</span></td>
                      <td><span>LYONS PARK &amp; RIDE (GETTY STA)</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>STONEHOUSE ROAD</span></td>
                      <td><span>VALLEY ROAD</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>VALLEY RD (512)</span></td>
                      <td><span>KING GEORGE RD. (CHURCH)</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>VALLEY RD (512)</span></td>
                      <td><span>DIVISON AVE.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>VALLEY RD (512)</span></td>
                      <td><span>S. NORTHFIELD RD.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>VALLEY RD (512)</span></td>
                      <td><span>VALLEY MALL</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>VALLEY RD (512)</span></td>
                      <td><span>MOUNTAIN AVE.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>PLAINFIELD AVE</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>KINGS SHOPPING CENTER</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>SNYDER AVE.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>BRIARWOOD DR.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>CENTRAL AVE</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>SOUTH ST.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>MAPLE AVE.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>PASSAIC AVE. (KINGS MARKET)</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>PINE GROVE AVE.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>NEW ENGLAND AVE.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>MORRIS AVE.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>BROAD ST.</span></td>
                      <td><span>MAPLE ST. (RR STA.)</span></td>
                      <td><span>S</span></td>
                    </tr>

                    <tr>
                      <td><span>BROAD ST.</span></td>
                      <td><span>OVERLOOK</span></td>
                      <td><span>S</span></td>
                    </tr>

                    <tr>
                      <td><span>BROAD ST.</span></td>
                      <td><span>ASHWOOD AVE.</span></td>
                      <td><span>S</span></td>
                    </tr>

                    <tr>
                      <td><span>SUMMIT PARK &amp; RIDE</span></td>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>PORT AUTHORITY BUS TERM</span></td>
                      <td><span>TERMINAL  BUS LANES</span></td>
                      <td><span>S</span></td>
                    </tr>

                    <tr>
                      <td style="background-color: #253d87;"><span class="color-w"><strong>WESTBOUND</strong></span></td>
                      <td style="background-color: #253d87;"><span  class="color-w"><strong>PABT TO BERNARDSVILLE</strong></span></td>
                      <td style="background-color: #253d87;"></td>
                    </tr>

                    <tr>
                      <td style="background-color: #f1f5f8;"><span style="color: #003366; " ><strong><span>ON STREET</span></strong></span></td>
                      <td style="background-color: #f1f5f8;"><span style="color: #003366;"><strong><span>AT STREET</span></strong></span></td>
                      <td style="background-color: #f1f5f8; text-align: center;"><span style="color: #003366;"><strong><span>DIR</span></strong></span></td>
                    </tr>


                    <tr>
                      <td><span>PORT AUTHORITY BUS TERM</span></td>
                      <td><span>TERMINAL  BUS LANES</span></td>
                      <td><span>S</span></td>
                    </tr>

                    <tr>
                      <td><span>SUMMIT PARK &amp; RIDE</span></td>
                      <td><span>SUMMIT PARK &amp; RIDE</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>BROAD ST.</span></td>
                      <td><span>ASHWOOD AVE.</span></td>
                      <td><span>N</span></td>
                    </tr>

                    <tr>
                      <td><span>BROAD ST.</span></td>
                      <td><span>OVERLOOK</span></td>
                      <td><span>N</span></td>
                    </tr>

                    <tr>
                      <td><span>BROAD ST.</span></td>
                      <td><span>MAPLE ST. (RR STA.)</span></td>
                      <td><span>N</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>MORRIS AVE.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>NEW ENGLAND AVE.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>PINE GROVE AVE.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>PASSAIC AVE. (KINGS MARKET)</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>MAPLE AVE.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>SOUTH ST.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>CENTRAL AVE</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>BRIARWOOD DR.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>SNYDER AVE.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>KINGS SHOPPING CENTER</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>SPRINGFIELD AVE.</span></td>
                      <td><span>PLAINFIELD AVE</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>VALLEY RD (512)</span></td>
                      <td><span>MOUNTAIN AVE.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>VALLEY RD (512)</span></td>
                      <td><span>VALLEY MALL</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>VALLEY RD (512)</span></td>
                      <td><span>S. NORTHFIELD RD.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>VALLEY RD (512)</span></td>
                      <td><span>DIVISON AVE.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>VALLEY RD (512)</span></td>
                      <td><span>KING GEORGE RD. (CHURCH)</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>STONEHOUSE ROAD</span></td>
                      <td><span>VALLEY ROAD</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>STONEHOUSE ROAD</span></td>
                      <td><span>LYONS PARK &amp; RIDE (GETTY STA)</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>S. FINLEY AVE.</span></td>
                      <td><span>CROSS ROAD</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>S. FINLEY AVE</span></td>
                      <td><span>W. HENRY ST</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>N. FINLEY AVE.</span></td>
                      <td><span>RIDGE ST.</span></td>
                      <td><span>E</span></td>
                    </tr>

                    <tr>
                      <td><span>RT 202 N.</span></td>
                      <td><span>N. FINLEY AVE</span></td>
                      <td><span>N</span></td>
                    </tr>

                    <tr>
                      <td><span>RT 202 N.</span></td>
                      <td><span>ACROSS FROM KINGS</span></td>
                      <td><span>N</span></td>
                    </tr>

                    <tr>
                      <td><span>RT 202 N.</span></td>
                      <td><span>BERNARDSVILLE TRAIN STA</span></td>
                      <td><span>E</span></td>
                    </tr>
                  </tbody>
                </table>


              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>
</div></div>
HTML;

    return $form ;
  }
add_shortcode('lakeland_bus_stops', 'lakeland_bus_stops_shortcode');
