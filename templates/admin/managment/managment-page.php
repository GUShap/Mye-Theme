<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin Order Management Page Template
 * 
 * @var array $orders_data
 * @var array $workshop_data
 * @var string $calendar_tamplate_path
 */
?>

<div id="order-managment">
    <h1 class="managment-title">ניהול מאי</h1>
    <div class="order-calendar-wrapper">
        <h4 class="order-title">הזמנות</h4>
        <div class="calendar">
            <?php 
            if(file($calendar_tamplate_path)) {
                $dates_data = $orders_data;
                require_once $calendar_tamplate_path;
            }
            ?>
        </div>
    </div>
    <div class="workshop-calendar-wrapper">
        <h4 class="worksho-title">סדנאות</h4>
        <!-- <div class="calendar"></div> -->
    </div>
</div>