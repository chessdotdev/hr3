
<?php
include '../includes/header.php';
include '../includes/activeEmployee.php';
include '../functions/pendingCount.php';
include '../includes/notificationsCount.php';
$sqlPresent = "SELECT COUNT(DISTINCT driver_id) AS present_today FROM employee_time_logs WHERE DATE(time_in) = CURDATE()";
$resultPresent = $conn->query($sqlPresent);
$presentCount = $resultPresent->fetch_assoc()['present_today'] ?? 0;

?>
  <!-- Dashboard -->
  <div class="content">
    <h1>Dashboard</h1>
    <div class="d-flex flex-column flex-md-row align-items-center gap-5 ">
      <div class="card mb-3" style="width: 18rem;">
            <div class="card-body ">
              <h5 class="card-title">Active</h5>

              <p class="card-tex fs-3 text-center"><?=$activeCount;?></p>
              </div>
        </div>

      <div class="card mb-3" style="width: 18rem;">
          <div class="card-body ">
            <h5 class="card-title">Shift Request Pending</h5>

            <p class="card-tex fs-3 text-center"><?=pendingCount('shift_req')?></p>
            </div>
      </div>
    

      <div class="card mb-3" style="width: 18rem;">
          <div class="card-body ">
            <h5 class="card-title">Claim Request Pending</h5>

            <p class="card-tex fs-3 text-center"><?=$claims[0]?></p>
            </div>
      </div>
      <div class="card mb-3" style="width: 18rem;">
          <div class="card-body ">
            <h5 class="card-title">Leave Request Pending</h5>

            <p class="card-tex fs-3 text-center"><?=pendingCount('leave_request')?></p>
            </div>
            
      </div>
      <div class="card mb-3" style="width: 18rem;">
          <div class="card-body ">
              <h5 class="card-title">Present Today</h5>
             <p class="card-tex fs-3 text-center"><?=$presentCount; ?></p>
            </div>
      </div>
    
</div>
<div class="container my-4">
<div class="card mb-4" style="width: 100%;">
  <div class="card-body">
    <h5 class="card-title">Claim Status Distribution</h5>
    <canvas id="claimPieChart" height="100"></canvas>
  </div>
</div>

</div>
     
  </div>

<script src="../assets/js/chart.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    fetch('../functions/claimsChartData.php')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('claimPieChart').getContext('2d');

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Approved', 'Rejected', 'Pending'],
                    datasets: [{
                        label: 'Claim Status Distribution',
                        data: [data.Approved, data.Rejected, data.Pending],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.7)',   // Green
                            'rgba(220, 53, 69, 0.7)',   // Red
                            'rgba(255, 193, 7, 0.7)'    // Yellow
                        ],
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(220, 53, 69, 1)',
                            'rgba(255, 193, 7, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    return `${label}: ${value} claims`;
                                }
                            }
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Pie chart load error:', error);
        });
});
</script>
