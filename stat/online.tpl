<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h1 class="h2">Online calls</h1>
      <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group mr-2">
          <button id = "online_update" type="button" class="btn btn-sm btn-primary">Live update</button>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
          This week
        </button>
      </div>
    </div>

    <div class="row">
      <div class="col-sm">
        <h3 class="text-center">ONLINE CALLS</h3>
        Active calls: {$count_online_calls}
        <table class="table table-bordered table-sm table-striped text-center" >
            <th>Start</th><th>SrcIP</th><th>client</th><th>account</th><th>duration</th><th>from</th><th>To</th><th>DstIP</th><th>term Client</th><th>Term Account</th>
          
                {foreach $online_calls as $call}
                <tr>
                    {foreach $call as $data}
                    <td >{$data}</td>
                    {/foreach}
                 </tr>
                {/foreach}
                
        </table>
      </div>
      
      
    </div>
   
  </main>
  <script>
    $("#online_update").click(function(){
       $("#online_update").removeClass('btn-outline-secondary'); 
       $("#online_update").toggleClass('btn-primary');
        //$("#online_update").button('toggle');
    });

    setInterval(function(){

      if (!$("#online_update").hasClass('btn-primary')) {
          return;
      }
      location.reload();
      }, 5000);
    </script>
 