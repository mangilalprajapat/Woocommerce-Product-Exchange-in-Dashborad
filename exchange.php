<?php
/*
Plugin Name: Exchange order Plugin
Plugin URI: 
description: A plugin to add exchange order 
Version: 1.0
Author: SUD
License: GPL2
*/

add_action('admin_head', 'my_custom_css');
function my_custom_css() {
  echo '<style>
       .exchange-refund-actions{
          text-align: center;
          padding-top: 20px;
        } 
        .do-exchange-manual{
            padding: 5px 12px !important;
            font-size: 14px !important;
            float:right;
        }
        .exchange-cancel{
            padding: 5px 12px !important;
            font-size: 14px !important;
            float: left;
        }
        .exchange_order_tb{
            text-align: center;
            margin-left: 0%;
            width:100%
        }
        .exchange_order_tb thead{
            font-size: 15px;
            font-weight: 600;
            background: #efefea;
        }
        
        tr.product-select-box {
        display: none;
        }
        
        #woocommerce-order-items .wc-order-data-row{
            text-align: left;
            border-bottom: 0px;
        }
        .Product_display{
            border-bottom: 1px solid #dfdfdf;
        }
         .post-type-shop_order .wc-order-bulk-actions .button.calculate-action{
            display: none !important;
  </style>';
}

add_action( 'woocommerce_order_item_add_action_buttons', 'wc_order_item_add_action_buttons_callback', 10, 1 );
function wc_order_item_add_action_buttons_callback( $order ) 
{
    $label = esc_html__( 'Exchange', 'woocommerce' );
    $slug  = 'exchange';
    ?>    
    <button type="button" id="exchange-button-item" class="button <?php echo $slug; ?>-items"><?php echo $label; ?></button>

    <?php
}
  
add_action( 'woocommerce_order_item_add_action_buttons', 'wc_order_item_add_action_button', 10, 1 );
function wc_order_item_add_action_button( $order ) 
{
    $shipping_total = $order->get_shipping_total();
    $subtotal = $order->get_subtotal();
    $main_total = $subtotal + $shipping_total;
    ?>
    <div class="wc-order-wrapper exchange-order-wrapper" style="display:none; padding-top: 30px;">
        <table cellpadding="10" cellspacing="10" class="wc-old-order woocommerce_order_items exchange_order_tb">
            <thead>
                <tr>
                    <td>Select</td>
                    <td>Product</td>
                    <td>Quantity</td>
                    <td>Total</td>
                </tr>
            </thead>
            <tbody id="order_line_items">
                <?php
                foreach ( $order->get_items() as $item_id => $item ) {
                    $name = $item->get_name();
                    $quantity = $item->get_quantity();
                    $total = $item->get_total();
                    $product_id = $item->get_product_id();
                    $product = $item->get_product();
                    $price = $product->price;
                    $oder_id = $order->get_id();
                    ?>            
                    <tr class="custom_old_order item">
                        <td><input type="checkbox" value="<?php echo $price; ?>" id="<?php echo $product_id; ?>_product_check" name="pcheck" data-product-id = "<?php echo $product_id; ?>" data-product-name = "<?php echo $name; ?>"  data-product-quantity = "<?php echo $quantity; ?>" data-product-total = "<?php echo $total; ?>" data-product-price ="<?php echo $price; ?>" class="product_check"></td>
                        <td>
                           <img src="<?php echo wp_get_attachment_url($thumbnail); ?>" />
                            <?php echo $name; ?>
                        </td>
                        <td>
                            <div class="view">
                                <small class="times">×</small> <?php echo $quantity; ?>     
                            </div>
                            <input type="number" id="quantity" class="product_checks input-text qty text" step="1" min="1" max="<?php echo $quantity; ?>" name="cart[]" value="1" title="Qty" onchange="calculate_sum(<?php echo $price; ?>,  this)" size="3" inputmode="numeric">
                        </td>
                        <td>
                            <div class="view">
                                <span class="woocommerce-Price-amount amount">
                                    <span class="woocommerce-Price-currencySymbol">£</span>
                                    <?php 
                                    if ( filter_var($total, FILTER_VALIDATE_INT) === false ) {
                                        echo $total;
                                    }else{
                                        echo $total.'.00';
                                    }
                                    
                                    ?>
                                </span>      
                            </div>
                            <div class="exchange">
                                <input type="text" name="exchange_total" class="exchange_total wc_input_price" size="3" value="<?php echo $price ; ?>"> 
                            </div>
                        </td>

                        </tr>
                        
                        <tr class="Product_display_<?php echo $product_id; ?> Product_display product-select-box">
                            <td> <span style="font-size: 17px; font-weight: 500;"> Choose Products</span></td>
                            <td colspan="3">
                                <?php
                                    $args = array( 'post_type' => 'product', 'posts_per_page' => -1 );
                                    $loop = new WP_Query( $args );
                                ?>
                             
                                <div class="wc-order-data-row wc-order-data-row-toggle wc-order-exchange-items " id="exchange-sudb-detail_<?php echo $product_id; ?>" data-ids="<?php echo $product_id; ?>">
                                       
                                    <select class="js-select2 select_exchange" name="product" id="new_products_data_<?php echo $product_id; ?>" data-id="<?php echo $product_id; ?>" data-order_id="<?php echo $oder_id; ?>" style="width:100%;">
                                   
                                        <option value="" direction: ltr;></option>
                                        <?php
                                        while ( $loop->have_posts() ) : $loop->the_post(); 
                                        $pricee = get_post_meta(get_the_ID(), '_price', true);
                                        ?>
                                        <option value="<?php the_ID(); ?>" data-pro-price="<?php echo $pricee; ?>"><?php the_title(); echo " ( ".get_woocommerce_currency_symbol()."".number_format(floatval($pricee), 2, '.', '')." )";?></option>

                                        <?php
                                         endwhile; wp_reset_query(); ?>
                                    </select> 
                                     <!-- <input type="hidden" id="postsId" name="postsId"> -->

                                    <!-- <div class="new_exchange_product" id="new_product_success"></div>
                                    <div class="refund_or_exchange_price"></div>

                                    <div class="success_new_order"></div> -->
                                </div>
                            </td>    
                        </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="wc-order-data-row wc-order-data-row-toggle wc-order-exchange-items" id="exchange-sud-detail" style="display:none;">
        <div class="clear"></div>
        <table class="wc-order-totals" style="border-top: 1px solid #999; margin-top:12px; padding-top:12px">
            <tbody>
                <tr>
                    <td>Total available to exchange:</td>
                    <td width="1%"></td>
                    <td>
                        <span class="woocommerce-Price-amount amount">
                            <span class="woocommerce-Price-currencySymbol">£</span>
                            <?php echo number_format(floatval($main_total), 2, '.', ''); ?>
                        </span> 
                    </td>
                </tr>
                <tr>
                    <td class="label">Exchange Amount:</td>
                    <td width="1%"></td>
                    <td class="total">
                        <input type="text" id="exchange_amount" name="refund_amount" class="wc_input_price woo-exchange-amount" readonly="">
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="clear"></div>
    </div>    
    <?php
    $args = array( 'post_type' => 'product', 'posts_per_page' => -1 );
    $loop = new WP_Query( $args );
    ?>  
    <div class="exchange_data"> 
        <input type="hidden" id="postsId" name="postsId">
        <div class="new_exchange_product" id="new_product_success">            
            <table border="1" style="border-collapse:collapse; display: none;" id="mytable">
                <thead style="text-align: center">
                    <tr>
                    	<td>Remove Product</td>
                        <td width="500">Product Name</td>
                        <td width="350">Quantity</td>
                        <td width="350">Price</td>
                    </tr>
                </thead>
                <tbody id="ex_tbody" style="text-align: center">
                    <tr>
                        <!-- <td>test</td>
                        <td>
                            <div class="view">
                            <small class="times">×</small>1</div>
                        </td>
                        <td>£ 00.00</td> -->
                    </tr>
                </tbody>
                <tfoot style="text-align: center">
                    <tr>
                        <td colspan="2">
                            <strong>Total Exchange Price<strong>
                        </td>
                        <td></td>
                        <td>£ <span id="ex_totals"></span></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="refund_or_exchange_price"></div>
        <div class="success_new_order"></div>     
    </div>
    <div class="exchange-refund-actions" style="display: none;">
        <!-- do-manual-refund -->
        <button type="button" class="button cancel-action exchange-cancel">Cancel</button>
        <input type="button" class="button button-primary do-exchange-manual" value="Confirm Exchange" value="<?php $address; ?>" data-order-id="<?php echo $order->get_id();?>"/>
    </div>
    <?php
}

add_action('woocommerce_admin_order_totals_after_tax', 'custom_admin_order_totals_after_tax', 10, 1 );
function custom_admin_order_totals_after_tax( $order_id ) 
{
    //$odr_id = $order->get_id();
    $paid_price = get_post_meta($order_id, 'paid_value', true); 
    $refund_price = get_post_meta($order_id, 'refund_value', true); 
    if(!empty($paid_price))
    {
        $label = __( 'Amount To Pay', 'woocommerce' );
        $value = $paid_price;
    }
    else
    {
        $label = __( 'Amount To Refund', 'woocommerce' );
        $value = $refund_price;        
    }
    ?>
    <tr>
        <td class="label"><?php echo $label; ?> : </td>
        <td width="1%"></td>
        <td class="custom-total"><?php echo $value ?></td>
    </tr>
    <?php
}

add_action( 'admin_footer', 'exchange_button_function' );
function exchange_button_function()
{
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.2.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
    <script>
    	var old_product_to_exchangeds = [];
    	jQuery(".product_check").on('click', function()
        {
            var product_id = jQuery(this).data("product-id");
            old_product_to_exchangeds.push(product_id);

            var ischecked= jQuery(this).is(':checked');
            if(ischecked){
            jQuery(".Product_display_" + product_id).removeClass('product-select-box');
            jQuery("#exchange-sudb-detail_" + product_id).css({"display":"block"});
            }else{
            jQuery("#exchange-sudb-detail_" + product_id).css({"display":"none"});
            jQuery(".Product_display_" + product_id).addClass('product-select-box');
            }
		});

    	var exchanged_products_array = [];
        jQuery('.select_exchange').on('change',function() 
        {
            var posts_id = jQuery(this).val();
            exchanged_products_array.push(posts_id);
        });

    	jQuery(".do-exchange-manual").on("click", function()
        {
            if (confirm('Are you sure you wish to process this exchange? ')) {
            var order_id = jQuery(this).data("order-id");
            var old_product_to_exchange = old_product_to_exchangeds;
			var exchanged_products = exchanged_products_array;
            var get_old_product_price = jQuery(".woo-exchange-amount").val();

            var ajax_url = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
            jQuery.ajax({
                type: "post",
                dataType : "json",
                url : ajax_url,
                data: {action : 'new_order_details', exchanged_products: exchanged_products, old_product_to_exchange: old_product_to_exchange, order_id: order_id},
                success: function(response) 
                {
                    if(response.status == 'success')
                    {
                        jQuery(".success_new_order").html("<p>Above product exchange request is done </p> <p>A new order is generated for your exchange request. Order <a href="+response.new_order_url+"> #"+response.new_order+" </a></p>");
                    }
                    else
                    {
                        alert("failure");
                    }
                },
                error:function(response)
                {
                    console.log(response);
                }
            });
          }
        });

        jQuery(document).ready(function () 
        {
            // jQuery(".product_check").click(function() 
            // { 
            //     var product_id = jQuery(this).data("product-id");
            //     var ischecked= jQuery(this).is(':checked');
            //     if(ischecked){
            //     jQuery(".Product_display_" + product_id).removeClass('product-select-box');
            //     }else{
            //     jQuery(".Product_display_" + product_id).addClass('product-select-box');
            //     }
            // });
            jQuery("#exchange-button-item").on('click', function()
            {
                 jQuery(".wc-order-wrapper").css({"display":"block"});
                 jQuery(".exchange-refund-actions").css({"display":"block"});
                 jQuery(".refund_or_exchange_price").css({"display":"block"});
                 jQuery(".new_exchange_product").css({"display":"block"});
            });
            jQuery(".cancel-action").click(function () 
            {
                jQuery("#exchange-sud-detail").css({"display":"none"});
                jQuery(".wc-order-wrapper").css({"display":"none"});
                jQuery(".exchange-refund-actions").css({"display":"none"});
                jQuery(".new_exchange_product").css({"display":"none"});
                jQuery(".refund_or_exchange_price").css({"display":"none"});

            });
            var old_amt = '';
            jQuery(".product_check").on('click', function()
            {
            	jQuery("#exchange-sud-detail").css({"display":"block"});
                var product_id = jQuery(this).data("product-id");
                var ids = jQuery(this).data("ids");

                var atLeastOneIsChecked = jQuery(this).is(":checked");
                if(atLeastOneIsChecked == true)
                {
	                var pricee = jQuery(this).data('product-total');
	                var get_qty = jQuery(this).parent().parent().find(".product_checks").val();
	                var left_pric = pricee * get_qty;
	                var left_price = Number(old_amt) + Number(left_pric);
	                old_amt = left_price;
	            }
	            else
	            {
	            	var pricee = jQuery(this).data('product-total');
	                var get_qty = jQuery(this).parent().parent().find(".product_checks").val();
	                var left_pric = pricee * get_qty;
	                var left_price = Number(old_amt) - Number(left_pric);
	                old_amt = left_price;
	            }
                
                
                if ( jQuery('.product_check').val(jQuery(this).is(':checked')) )
                {
                     var product_id = jQuery(this).data("product-id");
                }
                
                jQuery('#new_products_data_'+product_id).on('change', function() 
                {
                    var oder = jQuery(this).data("order_id");
                    var pro_price = jQuery(this).data("pro-price");
                    var posts_id = jQuery(this).val();
                    
                    jQuery('#postsId').val(posts_id);

                    var ajax_url = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
                    jQuery.ajax({
                        type : "post",
                        dataType : "json",
                        url : ajax_url,
                        data : {action: "get_post_details", posts_id : posts_id, order_id : oder, pro_price: pro_price},
                        success: function(response) 
                        {   
                            jQuery("#mytable").show();
                            if(response.status == 'success')
                            {
                                var tds;
                                var my_price = [];
                                jQuery("#mytable").each(function () 
                                {
                                    var newprices = response.data.price;
                                    var pricefloat = parseFloat(newprices).toFixed(2);

                                    tds = '<tr class="tr_row" data-po_id="'+response.post_id+'">';
                                    tds += '<td class="remove_pro" data-id="'+response.post_id+'"> X </td>';
                                    tds += '<td>' + response.data.product_name + '</td>';
                                    tds += '<td>' + 1 + '</td>';
                                    tds += '<td>' + '£ '+ '<span class="total_amt">'+ pricefloat + '</span></td>';
                                    tds += '</tr>';
                                    if (jQuery('tbody', this).length > 0) 
                                    {
                                        jQuery('tbody', this).append(tds);
                                    } 
                                    else 
                                    {
                                        jQuery(this).append(tds);
                                    }
                                });
                                
                                var totl = 0;
                                jQuery('.total_amt').each(function() 
                                {
                                	totl += Number(jQuery(this).text());
                                });

                                jQuery('#ex_totals').html(parseFloat(totl).toFixed(2)); 
                                var get_old_price = jQuery(".woo-exchange-amount").val();
                                var total_exchange_price = totl - get_old_price;
                                if(total_exchange_price > 0)
                                {
                                    var extra_val = "Amount to be Paid";
                                    jQuery(".refund_or_exchange_price").text(extra_val+': £'+parseFloat(total_exchange_price).toFixed(2));
                                    jQuery(".refund_or_exchange_price").css('font-weight', 'bold');
                                    jQuery(".refund_or_exchange_price").css('background-color', '#ccc');
                                    jQuery(".refund_or_exchange_price").css('padding', '10px');
                                    jQuery(".refund_or_exchange_price").css('border-radius', '5px');
                                }
                                else
                                {
                                    var refund_val = "Amount to be Refunded";
                                    jQuery(".refund_or_exchange_price").text(refund_val+': £'+Math.abs(total_exchange_price));  
                                    jQuery(".refund_or_exchange_price").css('font-weight', 'bold');
                                    jQuery(".refund_or_exchange_price").css('background-color', '#ccc');
                                    jQuery(".refund_or_exchange_price").css('padding', '10px');
                                    jQuery(".refund_or_exchange_price").css('border-radius', '5px'); 
                                }
                            }
                            else
                            {
                            }
                        },
                        error:function(response)
                        {
                            console.log(response);
                        }
                    });
                });

                var checkType = Number.isInteger(left_price); //true
                console.log('checkType ',checkType);
                if(checkType == 'true')
                {
                    jQuery('.woo-exchange-amount').val(parseFloat(left_price).toFixed(2));
                    jQuery('.woo-exchange-amount').text(parseFloat(left_price).toFixed(2));
                }
                else
                {
                    
                    jQuery('.woo-exchange-amount').val(parseFloat(left_price).toFixed(2));
                    jQuery('.woo-exchange-amount').text(parseFloat(left_price).toFixed(2));
                }
            });

            jQuery(".js-select2").select2({
                closeOnSelect : false,
                placeholder : "Select Products",
                allowClear: true,
                tags: true,
            });
        });
        function calculate_sum(amount, selective) 
        {
            jQuery(selective).parent().parent().find(".exchange_total").val(jQuery(selective).val()*amount)
        }

        jQuery(document).on("click", ".remove_pro", function()
        {
        	var pro_id = jQuery(this).data('id');
        	var totl = 0;
            jQuery('.total_amt').each(function() 
            {
            	totl += Number(jQuery(this).text());
            });
            var get_amt = jQuery(this).parent().find(".total_amt").text();
            var discount = Number(totl) - Number(get_amt);
            jQuery(this).parent().remove();
            jQuery('#ex_totals').html(discount); 
            var new_total_final = jQuery("#ex_totals").text();
            var get_old_price = jQuery(".woo-exchange-amount").val();
            console.log(new_total_final);
            console.log(get_old_price);
            var total_exchange_price = new_total_final - get_old_price;
            if(total_exchange_price > 0)
            {
                var extra_val = "Amount to be Paid";
                jQuery(".refund_or_exchange_price").text(extra_val+': £'+total_exchange_price);
                jQuery(".refund_or_exchange_price").css('font-weight', 'bold');
                jQuery(".refund_or_exchange_price").css('background-color', '#ccc');
                jQuery(".refund_or_exchange_price").css('padding', '10px');
                jQuery(".refund_or_exchange_price").css('border-radius', '5px');
            }
            else
            {
                var refund_val = "Amount to be Refunded";
                jQuery(".refund_or_exchange_price").text(refund_val+': £'+Math.abs(total_exchange_price));  
                jQuery(".refund_or_exchange_price").css('font-weight', 'bold');
                jQuery(".refund_or_exchange_price").css('background-color', '#ccc');
                jQuery(".refund_or_exchange_price").css('padding', '10px');
                jQuery(".refund_or_exchange_price").css('border-radius', '5px'); 
            }
        });
    </script>
    <?php
}

add_action( 'admin_head', 'custom_admin_head' );
function custom_admin_head() 
{
    ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-css/1.4.6/select2-bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />
   	<?php
}

add_action( 'admin_enqueue_scripts', 'my_admin_style');
function my_admin_style() 
{
    wp_enqueue_style( 'admin-style', plugin_dir_url(__FILE__) . 'style.css' );
}

add_action("wp_ajax_get_post_details", "get_post_details");
function get_post_details()
{
    $json = array();
    $post_ids = $_POST['posts_id'];
    $order_id = $_POST['order_id'];
    $pro_price = $_POST['pro_price'];
    if(!empty($post_ids))
    {
        $get_products = get_post($post_ids);
        $pro_array = array();
        $product = wc_get_product( $get_products->ID );
        $get_products->product_name = $product->get_name();
        $get_products->price = $product->get_price();
        $get_products->thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ));

        /*$get_tot = get_post_meta($order_id, 'product_price_total', true);
        if(!empty($get_tot))
        {
            $total = $get_tot + $get_products->price;
            update_post_meta($order_id, 'product_price_total', $total);
        }
        else
        {
            update_post_meta($order_id, 'product_price_total', $get_products->price);   
        }*/

        //$get_totals = get_post_meta($order_id, 'product_price_total', true);
        $json['data'] = $get_products;
        $json['totals'] = $pro_price;
        $json['post_id'] = $get_products->ID;
        $json['status'] = 'success';
    }
    else
    {
        $json['failure'] = 'failure';
    }
    echo json_encode($json);
    exit();
}

add_action( 'init', 'wps_register_custom_order_status' );
function wps_register_custom_order_status() 
{
    register_post_status( 'wc-exchange-requested', array(
        'label'                     => 'Exchange Requested',
        'public'                    => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list'    => true,
        'exclude_from_search'       => false,
        'label_count'               => _n_noop( 'Exchange Requested <span class="count">(%s)</span>', 'Exchange Requested <span class="count">(%s)</span>' )
    ) );

    register_post_status( 'wc-exchange-approved', array(
        'label'                     => __('Exchange Approved'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Exchange Approved <span class="count">(%s)</span>', 'Exchange Approved <span class="count">(%s)</span>' )
    ) );

    register_post_status( 'wc-exchange-cancelled', array(
        'label'                     => 'Exchange Cancelled',
        'public'                    => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list'    => true,
        'exclude_from_search'       => false,
        'label_count'               => _n_noop( 'Exchange Cancelled <span class="count">(%s)</span>', 'Exchange Cancelled <span class="count">(%s)</span>' )
    ) );
}

function wps_add_custom_order_statuses( $order_statuses ) 
{
    $new_order_statuses = array();
    foreach ( $order_statuses as $key => $status ) {
        $new_order_statuses[ $key ] = $status;
        if ( 'wc-processing' === $key ) {
            $new_order_statuses['wc-exchange-requested'] = ('Exchange Requested');
            $new_order_statuses['wc-exchange-approved'] = __('Exchange Approved');
            $new_order_statuses['wc-exchange-cancelled'] = __('Exchange Cancelled');
        }
    }
    return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'wps_add_custom_order_statuses' );

// getting all the data from new and older order 
add_action("wp_ajax_new_order_details", "new_order_details");
function new_order_details()
{
    global $post;
    $json = array();
    $order_id = $_POST['order_id'];
    $exchanged_products = $_POST['exchanged_products'];
    $old_product_to_exchange = $_POST['old_product_to_exchange'];
    $old_order = wc_get_order($order_id);
    
    $old_billing_address = $old_order->get_address('billing'); 
    $order = new WC_Order($order_id);
    if (!empty($order)) {
        $order->update_status( 'exchange-requested' );
    }
    if(!empty($old_product_to_exchange))
    {
        foreach ($old_order->get_items() as $item_id => $item) 
        {
        	foreach ($old_product_to_exchange as $key => $olds_pros) 
        	{
        		$pro_id = $item->get_product_id();
	            if($olds_pros == $pro_id)
	            {
	                $product = $item->get_product();
	            }
	            if($olds_pros == $pro_id)
	            {
	                $qty = $item->get_quantity();
	                wc_update_product_stock($product, $qty, 'increase');
	            }
	            else
	            {
	                $product = get_product($olds_pros);
	                $qty = $product->get_stock_quantity();
	                wc_update_product_stock($product, $qty, 'increase');
	            }
        	}
        }
    }
    foreach ($old_order->get_items() as $item_id => $item) 
    {
        $product_id = $item->get_product_id();
        foreach ($old_product_to_exchange as $key => $olds_pros) 
        {
	        if($product_id == $olds_pros)
	        {
	            $pro[] = $product_id;
	        }
	    }
    }
    
    $create_order = wc_create_order();
    if(!empty($exchanged_products))
    {
        foreach ($exchanged_products as $key => $value) 
        {
            $new_productts = get_product($value);
            $create_order->add_product( $new_productts, 1);
            $new_product_total += $new_productts->price;
        }
    }

    if(!empty($pro))
    {
        foreach ($pro as $keyy => $valuee) 
        {
            $old_ex_product = get_product($valuee);
            $create_order->add_product($old_ex_product , 1);
            $old_product_total += $old_ex_product->price;
        }
    }
    $create_order->set_address( $old_billing_address, 'billing' );
    $total_exchange_price = $new_product_total - $old_product_total;
    $new_order_id = $create_order->get_id();
    $amtt = '';
    if($total_exchange_price > 0)
    {
        $pay_val ='£'.$total_exchange_price;
        update_post_meta($new_order_id, 'paid_value', $pay_val, true);
        $amtt = "Amount To Pay: ". $pay_val;
    }
    if($total_exchange_price < 0)
    {
        $return_val= '£'.abs($total_exchange_price);
        update_post_meta($new_order_id, 'refund_value', $return_val, true);   
        $amtt = "Amount To Refund: ". $return_val;
    }
    $create_order->set_total($total_exchange_price);
    $create_order->calculate_totals();
    $create_order->update_status( 'exchange-requested', '', true ); 
    $create_order->set_status( 'exchange-requested', '', true ); 

    $create_order->save();
    
    $new_order_url = admin_url()."post.php?action=edit&post=".$create_order->get_id();
    $json['status'] = 'success';
    $json['new_order_url'] = $new_order_url;
    $json['new_order'] = $create_order->get_id();
    $json['new_product_total'] = $new_product_total;
    $json['pay_amount'] = $pay_val;
    $json['refund_amount'] = $return_val;
    echo my_custom_new_order_email($create_order->get_id());
    echo json_encode($json);
    exit();
}

function my_custom_new_order_email($order_id) 
{
	$paid_price = get_post_meta($order_id, 'paid_value', true); 
    $refund_price = get_post_meta($order_id, 'refund_value', true); 
    if(!empty($paid_price))
    {
        $label = __( 'Amount To Pay', 'woocommerce' );
        $value = $label.' '.$paid_price;
    }
    if(!empty($refund_price))
    {
        $label = __( 'Amount To Refund', 'woocommerce' );
        $value = $label.' '.$refund_price;    
    }
    $order = new WC_Order( $order_id );
    $billing_address = $order->get_formatted_billing_address(); // for printing or displaying on web page
    $shipping_address = $order->get_formatted_shipping_address();
    $email = $order->billing_email;
    $name = $order->billing_first_name.' '.$order->billing_last_name;
    $billing_phone = $order->billing_phone;
    $date = date('M d, Y');

    $data = '';
    $data .= "<table border='0' cellpadding='0' cellspacing='0' width='600'><tbody><tr>
    <td valign='top' style='background-color:#fdfdfd'>
    <table border='0' cellpadding='20' cellspacing='0' width='100%'>
    <tbody>
    <tr>
    <td valign='top' style='padding:48px'>
    <div style='color:#737373;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:14px;line-height:150%;text-align:left'>
    <span>
    <h2 style='font-size: 30px;color: #282c3f;font-family: 'Lato',sans-serif;font-style: normal;'>Hello <span style='font-weight: bold;'>$name!</span></h2>
    <p style='margin:0 0 16px;font-size: 25px;'>
    Your Order Exchange Request is received <span style='font-size: 12px; opacity: 0.6;font-weight: bold;    color: black;'> on $date</span>
    </p>
    <h2 style='font-size: 20px;'>Important!</h2>
    <p>Kindly hand over the item with all original tags intact, including the MRP tag</p>
    </span>
    <h2 style='font-size: 25px;color: #282c3f;'>Exchange Details</h2>
    <p style='color: #94969f!important;font-size: 13px;letter-spacing: 0.39px;'>Your exchange order ID</p>
    <h2 style='color:#557da1;display:block;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:16px 0 8px;text-align:left'>
    Order # $order_id ( $date )
    </h2>
    <div>
    <div>";
    if( sizeof( $order->get_items() ) > 0 ) {           
        $data   .=    "<table cellspacing='0' cellpadding='6' style='width:100%;border:1px solid #eee' border='1'>
        <thead>
        <tr>
        <th scope='col' style='text-align:left;border:1px solid #eee;padding:12px'>
        Product
        </th>
        <th scope='col' style='text-align:left;border:1px solid #eee;padding:12px'>
        Quantity
        </th>
        <th scope='col' style='text-align:left;border:1px solid #eee;padding:12px'>
        Price
        </th>
        </tr>
        </thead>
        <tbody>";
        $data   .= $order->email_order_items_table( false, true );            
        $data   .=  "</tbody><tfoot>";
        // if ( $totals = $order->get_order_item_totals() ) {
        //     $i = 0;
        //     foreach ( $totals as $total ) {
        //     $i++;
        //     $label =    $total['label'];
        //     $value = $total['value'];
            $data .= "<tr>
            <th scope='row' colspan='3' style='text-align:left; border: 1px solid #eee;'>".$value."</th>
            <th scope='row' colspan='3' style='text-align:left; border: 1px solid #eee;'>".$order->get_checkout_payment_url()."</th>
            </tr>";
            //$data .= add_action('woocommerce_admin_order_totals_after_tax', 'custom_admin_order_totals_after_tax', 10, $order_id );
        //     }
            
        // }
        $data .= "</tfoot></table>";
       // $data .= '<div class="total">'.$amtt.'</div>';
    }

    $data .=        
    "<span>
    <h2 style='color:#557da1;display:block;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:16px 0 8px;text-align:left'>
    Customer details
    </h2>
    <p style='margin:0 0 16px'>
    <strong>Email:</strong>
    <a href='mailto:' target='_blank'>
    $email
    </a>
    </p>
    <p style='margin:0 0 16px'>
    <strong>Tel:</strong>
    $billing_phone
    </p>
    <table cellspacing='0' cellpadding='0' style='width:100%;vertical-align:top' border='0'>
    <tbody>
    <tr>
    <td valign='top' width='50%' style='padding:12px'>
    <h3 style='color:#557da1;display:block;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:16px;font-weight:bold;line-height:130%;margin:16px 0 8px;text-align:left'>Billing address</h3>
    <p style='margin:0 0 16px'> $billing_address </p>
    </td>
    <td valign='top' width='50%' style='padding:12px'>
    <h3 style='color:#557da1;display:block;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:16px;font-weight:bold;line-height:130%;margin:16px 0 8px;text-align:left'>Shipping address</h3>
    <p style='margin:0 0 16px'> $shipping_address </p>
    </td>
    </tr>
    </tbody>
    </table>
    </span>
    </div>
    </td>
    </tr>
    </tbody>
    </table>
    </td>
    </tr>
    </tbody>
    </table>";
    $mailer = WC()->mailer();
    $subject = 'Your Exchange order Request has been Received';
    $mailer->send($order->billing_email, $subject, $mailer->wrap_message( $subject, $data ), '', '' );
}