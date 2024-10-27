<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Calendar Template
 * 
 * @var array $dates_data
 */

$calendar_dates = fill_calendar_dates_by_month($dates_data);
$processing_color = '#f1c40f';
$completed_color = '#2ecc71';
$first_month_key = !empty($calendar_dates) ? array_key_first($calendar_dates) : null;
$last_month_key = !empty($calendar_dates) ? array_key_last($calendar_dates) : null;
?>
<?php if (count($calendar_dates) > 1): ?>
    <div class="arrows-container">
        <button class="next-month arrow-button">&#9650;</button>
        <button class="prev-month arrow-button">&#9650;</button>
    </div>
<?php endif; ?>
<div class="dates-wrapper">
    <div class="inner" style="grid-template-columns:repeat(<?php echo count($calendar_dates) ?>, 100%)">
        <?php foreach ($calendar_dates as $month_key => $dates):
            $is_first_month = $month_key === $first_month_key;
            $is_last_month = $month_key === $last_month_key;
            $month_classes = 'calendar-month';
            $month_classes .= $is_first_month ? ' first-month centered' : ($is_last_month ? ' last-month' : '');
            ?>
            <?php
            // Extract year and month, get Hebrew month name
            [$year, $month] = explode('-', $month_key);
            $month_name = get_hebrew_month_name($month);
            ?>

            <div class="<?php echo $month_classes ?>">
                <h2><?php echo "{$month_name} {$year}"; ?></h2>

                <div class="calendar-days-of-week">
                    <?php foreach (get_hebrew_days_of_week() as $day): ?>
                        <div class="day-of-week"><?php echo $day; ?></div>
                    <?php endforeach; ?>
                </div>

                <div class="calendar-dates">
                    <?php foreach ($dates as $date => $order_data): ?>
                        <?php
                        $day_of_week = get_day_of_week_number($date);
                        ?>
                        <div class="calendar-date day-<?php echo $day_of_week; ?>"
                            style="grid-column: <?php echo $day_of_week; ?>;">
                            <div class="date-heading"><?php echo format_hebrew_date($date); ?></div>

                            <div class="date-content">
                                <?php if (!empty($order_data)): ?>
                                    <?php foreach ($order_data as $order):
                                        $bgc = $order['status'] === 'processing' ? $processing_color : $completed_color;
                                        ?>
                                        <div class="order-info">
                                            <div class="order-details">
                                                <span class="status" style="background-color:<?php echo $bgc ?>"></span>
                                                <a href="<?php echo $order['order_link']; ?>" target="_blank">
                                                    <?php echo "הזמנה {$order['order_id']}"; ?>
                                                </a>
                                            </div>
                                            <ul class="order-items">
                                                <?php foreach ($order['items'] as $item): ?>
                                                    <li><?php echo "{$item['name']} - כמות: {$item['qty']}"; ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="no-orders">אין הזמנות</p>
                                <?php endif; ?>
                            </div>

                            <div class="date-footing"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>