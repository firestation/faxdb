<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h1 class="h2">Dashboard</h1>
      <div class="btn-toolbar mb-2 mb-md-0">
        
        <div class="btn-group mr-2">
          <button id = "online_update" type="button" class="btn btn-sm btn-outline-secondary">Live update</button>
          <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
          <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
          This week
        </button>
      </div>
    </div>

    <div class="row" id = "graph_total">
      <canvas class="my-4 w-100 chartjs-render-monitor" id="myChart" width="991" height="417" style="display: block; height: 334px; width: 793px;"></canvas>
    </div>
        
       
         
     </main>
     <script>
         $("#online_update").click(function(){
            $("#online_update").removeClass('btn-outline-secondary'); 
            $("#online_update").toggleClass('btn-primary');
             //$("#online_update").button('toggle');
         });

        function GetChoice() {
            var returned = "";
            $.ajax({
                    async: false,
                    cache: false,
                    type: "POST",
                    url: "get_cps.php",
                    data: { name: "John"}
                    }).done(function( msg ) {                        
                            returned = msg;
                    });
              
             return returned;
      }
    
  
        
                 
      var r = GetChoice();
      var timeFormat = 'YYYY-MM-DD HH:mm:ss';
    
        function newDate(days) {
                return moment().add(days, 'd').toDate();
            }
        
            function newDateString(days) {
                return moment().add(days, 'd').format(timeFormat);
            }
        
      var ctx = document.getElementById('myChart').getContext('2d');
        
      //alert(myChart);
      var myChart = new Chart(ctx, {

          type: 'line',
          data: {
              labels: [],
              datasets: [{
                  label: 'Total CPS',
                  data: [],
                  backgroundColor: [
                      'rgba(255, 99, 132, 0.2)',
                  ],
                  borderColor: [
                      'rgba(255, 99, 132, 1)',
                  ],
                  borderWidth: 1
              }  
                
            ]
          },
          options: {
           
            animation: false,

            
            
          }



      });
      
      setInterval(function(){

        if (!$("#online_update").hasClass('btn-primary')) {
            exit;
        }

        var r = GetChoice();
        var date = new Date();
        myChart.data.datasets[0].data.push(r);
        var times = (date.getMinutes() < 10 ? "0" : "") + date.getMinutes() + ":" + (date.getSeconds() < 10 ? "0" : "") + date.getSeconds();
        myChart.data.labels.push(times);
       
        myChart.update();

        }, 1000);
    

      

</script>



    