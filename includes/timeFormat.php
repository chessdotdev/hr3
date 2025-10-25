<?php
 $start = new DateTime($shift_start);
 $end = new DateTime($shift_end);
 
 $hFormat = (int)$start->format('H'); 
 $start = date("g:i A", strtotime($shift_start));  // example outputs: 9:30 AM
 $end = date("g:i A", strtotime($shift_end));  // example outputs: 9:30 AM

// echo $start;
 if ($hFormat < 12) {
     $shift_type = 'Morning Shift';
 } else {
     $shift_type = 'Night Shift';
 }