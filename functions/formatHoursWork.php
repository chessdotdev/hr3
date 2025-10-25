<?php  

function formatHoursMinutes($decimalHours) {
            $hours = floor($decimalHours);
            $minutes = round(($decimalHours - $hours) * 60);
            return "{$hours} hrs {$minutes} mins";
        }