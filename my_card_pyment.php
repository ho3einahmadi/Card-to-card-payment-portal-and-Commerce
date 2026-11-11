<?php

/**
 * Plugin Name: Bank Card-to-Card Payment Gateway for WooCommerce
 * Description: Card-to-card manual payment gateway with a bank-app style thank-you page. | درگاه کارت‌به‌کارت دستی برای ووکامرس.
 * Version: 3.2
 * Author: Hossein Ahmadi
 * Text Domain: bb-card-to-card
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'plugins_loaded', 'init_custom_card_transfer_gateway' );

function init_custom_card_transfer_gateway() {

    if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
        return; // WooCommerce is not active.
    }

    class WC_Gateway_Custom_Card extends WC_Payment_Gateway {

        public function __construct() {
            $this->id                 = 'custom_card_transfer';
            $this->method_title       = 'کارت به کارت بن بیگی';
            $this->has_fields         = false;

            $this->init_form_fields();
            $this->init_settings();

            $this->title        = $this->get_option( 'title' );
            $this->card_number  = $this->get_option( 'card_number' );
            $this->account_name = $this->get_option( 'account_name' );
            $this->receipt_url  = $this->get_option( 'receipt_url' );

            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
            add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array( 'title' => 'فعالسازی', 'type' => 'checkbox', 'default' => 'yes' ),
                'title' => array( 'title' => 'عنوان درگاه', 'type' => 'text', 'default' => 'پرداخت کارت به کارت' ),
                'card_number' => array( 'title' => 'شماره کارت', 'type' => 'text', 'placeholder' => 'xxxx-xxxx-xxxx-xxxx' ),
                'account_name' => array( 'title' => 'نام صاحب حساب', 'type' => 'text' ),
                'receipt_url' => array( 'title' => 'لینک ارسال رسید', 'type' => 'text', 'default' => 'https://t.me/Benyaminbeygi23', 'description' => 'Telegram/Bale link where customers send the receipt.' ),
            );
        }

        public function process_payment( $order_id ) {
            $order = wc_get_order( $order_id );
            
            // تنظیم وضعیت به "در انتظار پرداخت"
            $order->update_status( 'on-hold', 'در انتظار واریز کارت به کارت توسط مشتری' );
            
            // اضافه کردن یادداشت برای سفارش
            $order->add_order_note( 'مشتری به صفحه کارت به کارت هدایت شد. در انتظار تایید پرداخت.' );
            
            wc_empty_cart();
            
            return array( 
                'result' => 'success', 
                'redirect' => $this->get_return_url( $order ) 
            );
        }

        public function thankyou_page($order_id) {
            $order = wc_get_order( $order_id );
            if ( ! $order || $order->get_payment_method() !== $this->id ) {
                return;
            }
            $total = $order->get_formatted_order_total();
            ?>
            <style>
                /* استایل کلی ظرف */
                .bb-wrapper {
                    background: #121212; 
                    border-radius: 35px; 
                    padding: 30px 20px; 
                    max-width: 480px; 
                    margin: 40px auto; 
                    font-family: 'Tahoma', sans-serif; 
                    color: #fff; 
                    direction: rtl;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
                }

                /* سربرگ */
                .bb-header { margin-bottom: 25px; text-align: center; }
                .bb-header h2 { color: #e5c07b; margin: 0; font-size: 22px; font-weight: bold; }
                .bb-header span { color: #666; font-size: 11px; letter-spacing: 1px; }

                /* کارت بانکی */
                .bb-card {
                    background: linear-gradient(135deg, #7a28ff 0%, #4a00e0 100%);
                    border-radius: 24px;
                    padding: 25px;
                    margin-bottom: 30px;
                    height: 190px;
                    position: relative;
                    box-shadow: 0 15px 30px rgba(74, 0, 224, 0.4);
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    text-align: left; /* برای شماره کارت */
                }
                .bb-card-top { display: flex; justify-content: space-between; align-items: flex-start; }
                .bb-chip { width: 45px; height: 35px; background: linear-gradient(135deg, #f1c40f, #f39c12); border-radius: 6px; }
                .bb-card-number { 
                    font-size: 22px; 
                    letter-spacing: 4px; 
                    color: #fff; 
                    text-align: center; 
                    font-weight: bold;
                    margin: 15px 0;
                }
                .bb-card-holder { font-size: 14px; opacity: 0.9; text-align: right; }

                /* ردیف‌های اطلاعاتی */
                .bb-info-row {
                    background: rgba(255, 255, 255, 0.05);
                    border: 1px solid rgba(255, 255, 255, 0.08);
                    border-radius: 18px;
                    padding: 14px 18px;
                    margin-bottom: 12px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .bb-label { font-size: 12px; color: #999; }
                .bb-value { font-size: 14px; font-weight: bold; }

                /* دکمه‌ها */
                .bb-copy-btn {
                    background: rgba(142, 45, 226, 0.2);
                    border: 1px solid #8e2de2;
                    color: #8e2de2;
                    padding: 4px 12px;
                    border-radius: 8px;
                    font-size: 11px;
                    cursor: pointer;
                    transition: 0.3s;
                }
                .bb-copy-btn:hover { background: #8e2de2; color: #fff; }

                .bb-tg-btn {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: #6200ee;
                    color: #fff !important;
                    text-decoration: none;
                    padding: 16px;
                    border-radius: 20px;
                    margin-top: 25px;
                    font-weight: bold;
                    font-size: 14px;
                    transition: 0.3s;
                }
                .bb-tg-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(98, 0, 238, 0.4); }

                .bb-footer-id { text-align: center; margin-top: 15px; font-size: 11px; color: #444; }
                
                /* پیام هشدار */
                .bb-alert {
                    background: rgba(255, 152, 0, 0.1);
                    border: 1px solid rgba(255, 152, 0, 0.3);
                    border-radius: 15px;
                    padding: 15px;
                    margin-bottom: 20px;
                    text-align: center;
                    color: #ff9800;
                    font-size: 13px;
                }
            </style>

            <div class="bb-wrapper">
                <div class="bb-header">
                    <h2>بِـن بیگی</h2>
                    <span>درگاه پرداخت امن</span>
                </div>

                <div class="bb-alert">
                    ⚠️ لطفاً پس از واریز، رسید را در تلگرام ارسال کنید
                </div>

                <div class="bb-card">
                    <div class="bb-card-top">
                        <div class="bb-chip"></div>
                        <span style="font-size: 11px; font-weight: bold;">DEBIT CARD</span>
                    </div>
                    <div class="bb-card-number"><?php echo esc_html( $this->card_number ); ?></div>
                    <div class="bb-card-holder"><?php echo esc_html( $this->account_name ); ?></div>
                </div>

                <div class="bb-info-row">
                    <span class="bb-label">مبلغ نهایی:</span>
                    <span class="bb-value" style="color: #4caf50;"><?php echo $total; ?></span>
                </div>

                <div class="bb-info-row">
                    <div style="text-align: right;">
                        <span class="bb-label" style="display:block;">شماره کارت</span>
                        <span class="bb-value" id="card-raw"><?php echo esc_html( $this->card_number ); ?></span>
                    </div>
                    <button class="bb-copy-btn" onclick="copyCard()">کپی کارت</button>
                </div>

                <div class="bb-info-row">
                    <span class="bb-label">شماره سفارش:</span>
                    <span class="bb-value">#<?php echo esc_html( $order->get_order_number() ); ?></span>
                </div>

                <a href="<?php echo esc_url( $this->receipt_url ); ?>" target="_blank" rel="noopener noreferrer" class="bb-tg-btn">
                    <span>📤 ارسال رسید در بله</span>
                </a>

                <div class="bb-footer-id">@benbeygicom</div>
            </div>

            <script>
            function copyCard() {
                var text = <?php echo wp_json_encode( str_replace( array( ' ', '-' ), '', (string) $this->card_number ) ); ?>;
                navigator.clipboard.writeText(text).then(function() {
                    alert("✅ شماره کارت کپی شد!");
                });
            }
            </script>
            <?php
        }
    }
}

add_filter( 'woocommerce_payment_gateways', 'add_custom_card_transfer_gateway' );
function add_custom_card_transfer_gateway( $gateways ) {
    $gateways[] = 'WC_Gateway_Custom_Card';
    return $gateways;
}
