<?php
// Calibration settings (adjust these values as needed)
$offsets = ['year' => -1, 'month' => 0, 'day' => -1, 'hour' => 0, 'minute' => 0];

date_default_timezone_set('Asia/Tehran');

function convert_to_persian_numerals($number) {
    return str_replace(range(0, 9), ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'], $number);
}

function get_day_of_week($g_year, $g_month, $g_day) {
    $days_of_week = ['یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه'];
    return $days_of_week[(new DateTime("$g_year-$g_month-$g_day"))->format('w')];
}

function gregorian_to_jalali($g_year, $g_month, $g_day, $g_hour, $g_minute, $g_second, $offsets) {
    $g_days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    if (($g_year % 4 == 0 && $g_year % 100 != 0) || ($g_year % 400 == 0)) $g_days_in_month[1] = 29;
    
    // Apply day offset
    $total_days = array_sum(array_slice($g_days_in_month, 0, $g_month - 1)) + $g_day + $offsets['day'];

    $jalali_year = $g_year - 621 + $offsets['year'];
    if ($total_days > 79) {
        $total_days -= 79;
        if ($total_days > 186) {
            $jalali_year++;
            $total_days -= 186;
            $jalali_month = 7 + (int)($total_days / 30);
            $jalali_day = ($total_days % 30) + 1;
        } else {
            $jalali_month = 1 + (int)($total_days / 31);
            $jalali_day = ($total_days % 31) + 1;
        }
    } else {
        $jalali_year--;
        $total_days += 10;
        $jalali_month = 12 + (int)($total_days / 30);
        $jalali_day = ($total_days % 30) + 1;
    }


    // Apply month offset
    $jalali_month += $offsets['month'];
    if ($jalali_month > 12) {
        $jalali_month -= 12;
        $jalali_year++;
    } elseif ($jalali_month < 1) {
        $jalali_month += 12;
        $jalali_year--;
    }

    $jalali_months = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];
    
 // Adjust hour and minute with offsets
    $adjusted_hour = ($g_hour + $offsets['hour']) % 24;
    $adjusted_minute = ($g_minute + $offsets['minute']) % 60;

    // Determine the greeting based on the adjusted hour
    if ($adjusted_hour >= 5 && $adjusted_hour < 11) {
        $greeting = "صبح بخیر"; // Good Morning
    } elseif ($adjusted_hour >= 11 && $adjusted_hour < 16) {
        $greeting = "ظهر بخیر"; // Good Afternoon
    } elseif ($adjusted_hour >= 16 && $adjusted_hour < 20) {
        $greeting = "عصر بخیر"; // Good Evening
    } else {
        $greeting = "شب بخیر"; // Good Night
    }

    return '<span style="font-size: 50px;letter-spacing:-1px;">' . 
           get_day_of_week($g_year, $g_month, $g_day) . '<br>' . '</span>' .
           '<span style="font-size: 100px;letter-spacing:5px;color:#58cc60;-webkit-text-stroke: 1px #fff;">' . 
           convert_to_persian_numerals(sprintf('%02d', $adjusted_hour)) . '</span>' .
           '<span class="blinking-colon">:</span>' .
           '<span style="font-size: 100px;letter-spacing:5px;color:#58cc60;-webkit-text-stroke: 1px #fff;">' . 
           convert_to_persian_numerals(sprintf('%02d', $adjusted_minute)) . '</span>' . '<br>' . 
           '<span style="font-size:40px;color:#eccf00;">' . 
           convert_to_persian_numerals($jalali_day) . $jalali_months[$jalali_month - 1]  .  '</span>' .
           '<span style="font-size: 40px;color:#00eca2;">' . 
           convert_to_persian_numerals($jalali_year) . ' <br> '  .
           '<span style="font-size:22px;color:#fff;margin-bottom:30px;display:block;">' . $greeting .'</span>';
}
function get_current_jalali_datetime($offsets) {
    $current_date = new DateTime();
    return gregorian_to_jalali($current_date->format('Y'), $current_date->format('m'), $current_date->format('d'), 
                                $current_date->format('H'), $current_date->format('i'), $current_date->format('s'), 
                                $offsets);
}

// Use the offset variables defined at the top
echo get_current_jalali_datetime($offsets);
?>