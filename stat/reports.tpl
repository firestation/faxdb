<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h1 class="h2">Reports</h1>
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
    <div class="modal"><!-- Place at bottom of page --></div>    

    <form id="del_try_id" method="POST">
      <input type=hidden id="input_del_try_id" name="del_try_id">
    </form>

    <form id="try_id_report" method="POST" action="index.php?main_page=reports">
      <input type=hidden name="show_only" value="try">
      <input type=hidden id="input_try_id" name="try_id_report">
    </form>
    
    

    <form id="filter_form" method="POST">
      <input type=hidden id="input_cmp_id" name="cmp_id_report">
    </form>
    <form id="export_calls_form" method="POST"></form>
    <form id="export_cmp_form" method="POST"></form>
    <form id="show_cmp_form" method="POST"></form>
  

    
    <div class="row align-items-center">
      <div class="col-1  ">
         <input type="radio" {$show_only_cmp} form = "filter_form" class=" form-control show_only_radio" name="show_only" value="companie">
          
      </div>
        
          <span>Show only companie</span>
        
    </div>
    <div class="row align-items-center">
      <div class="col-1  ">
        <input type="radio" {$show_only_try} form = "filter_form" class=" form-control show_only_radio" name="show_only" value="try">
          
      </div>
        
          <span>Show only try</span>
        
    </div>
    <div class="row align-items-center">
      <div class="col-1  ">
         <input type="radio" {$show_running_try} form = "filter_form" class=" form-control show_only_radio" name="show_only" value="running_try">
          
      </div>
        
          <span>Show last run</span>
        
    </div>

    
    <div class="row">
      <h3>Companie tasks</h3>

      <table class="table table-bordered table-sm table-striped text-center table-responsive" >
        <thead class="table-primary">

          <th>   cmp_id</th>
          <th>   try_id</th>
          <th>  Started</th>
          
          
          <th>   Action</th>
          <th>calls_in_progress</th>
          <th>calls: ok / fail </th>
          <th>fax: sent / fail </th>
          <th>Talk duration</th>
          <th>Price per fax, USD</th>
          <th>Total Cost,USD</th>
          <th>cmp_duration</th>
          <th>  Updated</th>
        </thead>
        <!---<tr class="table-active">
        
        <td><input form="filter_form" type="text" class="filter_input form-control" name="filter_id"></input></td>
        <td><input form="filter_form" type="text" class="filter_input form-control" name="filter_created"></input></td>
        <td><input form="filter_form" type="text" class="filter_input form-control" name="filter_num" value='{$filter_num}'></input></td>
        <td><input form="filter_form" type="text" class="filter_input form-control" name="filter_cmp" value='{$filter_cmp}'></input></td>
        <td><input form="filter_form" type="text" class="filter_input form-control" name="filter_status"></input></td>
        <td></td>
        </tr>-->
            {foreach $tasks as $task}
            <tr>
               
              <td><button class="btn btn-link cmp_buttons" data-id="{$task.cmp_id}" >{$task.cmp_id}</button></td>
              <td><button class="btn btn-link try_buttons" data-id="{$task.try_id}" >{$task.try_id}</button></td>
              <td>{$task.started}</td>
              
              <td><button class="btn btn-danger del_buttons" data-id="{$task.try_id}" >DEL</button></td>
              <td>{$task.calls_in_progress}</td>
              <td ><b>{$task.success_calls_percent}%</b> <br> {$task.calls_answered} / {$task.calls_failed}</td>
              <td><b>{$task.sent_percent}% </b> <br>{$task.fax_sent} / {$task.fax_failed} </td>
              <td>{$task.cmp_talk_duration}</td> 
              <td>{$task.cmp_price_per_fax}</td>               
              <td>{$task.cmp_total_cost}</td>              
              <td>{$task.cmp_duration}</td>  
              <td>{$task.finished}</td> 
             </tr>
            {foreachelse}
            <tr><td colspan=16  >No tasks</td></tr>
            {/foreach}
            
    </table>
    
    </div>

    <div class="row">
      <h3>Details try:</h3>

      <table class="table table-bordered table-sm table-striped text-left table-responsive" >
        <thead class="table-primary">

          <th>   count</th>
          <th>   Mins</th>
          <th>  faxstatus</th>
          <th>  faxerror</th>
          <th>  sip_reason</th>
          

        </thead>

            {foreach $details as $detail}
            <tr>

              <td>{$detail.count}</td> 
              <td>{$detail.mins}</td>               
              <td>{$detail.faxstatus}</td>              
              <td>{$detail.faxerror}</td>  
              <td>{$detail.sip_reason}</td>  
             </tr>
            {foreachelse}
            <tr><td colspan=16  >no details</td></tr>
            {/foreach}
            
    </table>
    
    </div>


    <div class="row">
      ..
    </div>
    <div class="row">
      <h3>Filters</h3>
      <div class="col-5">


      </div>
    </div>

    <div class="row">
        <!--<div class="input-group">
            <fieldset class=""  >
                <legend>Group option:</legend>
                <input form="filter_form" class="filter_group" type="checkbox" {$group_by_cmp} name="group_by_cmp"> by Companie </input><br>
                <input form="filter_form" class="filter_group" type="checkbox" {$group_by_num}  name="group_by_num"> by Number </input><br>

            </fieldset>
          </div>-->

          <h3>Calls report</h3>

          <button id="export_calls_btn" form="export_calls_form" name="export_try_id" class="btn btn-success" value="{$try_id}">Export Try</button>
          <button id="export_cmp_btn" form="export_cmp_form" name="export_cmp_id" class="btn btn-success" value="{$cmp_id}">Export Companie</button>
          

        <table class="table table-bordered table-sm table-striped text-center table-responsive" >
            <thead class="table-primary">

              <th>           id</th> 
              <th>  cmp_call_id</th> 
              <th>       cmp_id</th>
              <th>       try_id</th>
              <th>  try_created</th>
              <th>       number</th> 
              <th>     uniqueid</th> 
              <th>    ring_time</th> 
              <th>    talk_time</th> 
              <th>    faxstatus</th> 
              <th>      faxmode</th> 
              <th>     faxpages</th> 
              <th>     faxerror</th>
              <th>      host_id</th>
              <th>     sip_code</th>
              <th>   sip_reason</th> 
              <th>      created</th> 
              <th>      updated</th> 
                
            </thead>
            <!---<tr class="table-active">
            
            <td><input form="filter_form" type="text" class="filter_input form-control" name="filter_id"></input></td>
            <td><input form="filter_form" type="text" class="filter_input form-control" name="filter_created"></input></td>
            <td><input form="filter_form" type="text" class="filter_input form-control" name="filter_num" value='{$filter_num}'></input></td>
            <td><input form="filter_form" type="text" class="filter_input form-control" name="filter_cmp" value='{$filter_cmp}'></input></td>
            <td><input form="filter_form" type="text" class="filter_input form-control" name="filter_status"></input></td>
            <td></td>
            </tr>-->
                {foreach $reports as $report}
                <tr>
                   
                  <td>{$report.id}</td> 
                  <td>{$report.cmp_call_id}</td> 
                  <td>{$report.cmp_id}</td>
                  <td>{$report.try_id}</td>
                  <td>{$report.try_created}</td>
                  <td>{$report.number}</td> 
                  <td>{$report.uniqueid}</td> 
                  <td>{$report.ring_time}</td> 
                  <td>{$report.talk_time}</td> 
                  
                  <td>{$report.faxstatus}</td> 
                  <td>{$report.faxmode}</td> 
                  <td>{$report.faxpages}</td> 
                  <td>{$report.faxerror}</td>
                  <td>{$report.host_id}</td>
                  <td>{$report.sip_code}</td>
                  <td>{$report.sip_reason}</td> 
                  <td>{$report.created}</td> 
                  <td>{$report.updated}</td> 
                   
                    
                 </tr>
                 
                {foreachelse}
                <tr><td colspan=16  >No calls</td></tr>
                {/foreach}
        </table>
        <nav aria-label="Page navigation example">
          <ul class="pagination">
            <li class="page-item"><a class="page-link" href="index.php?main_page=reports&set_page={$prevision_page}">Previous</a></li>
            {foreach $pages as $page}
            <li class="page-item"><a class="page-link" href="index.php?main_page=reports&set_page={$page.num}&try_id_report={$try_id}">{$page.num}</a></li>
            {/foreach}
          </ul>
        </nav>
        </div>
    </main>
    <script>
       $body = $("body");

        $(".filter_input").on('keypress',function(data) {
             if(data.which == 13) {
                alert("You pressed enter! " + $(this).val() + " " + $(this).data("action") + " " + $(this).is( ":checked" ));
                $('#filter_form').submit();
            }
        });
                
        $(".filter_group").change(function(data) {
            $body.addClass("loading");
            $('#filter_form').submit();
            
        });
        $(".show_only_radio").click(function(data) {
            $body.addClass("loading");
            $('#filter_form').submit();
            
        });
        $('.try_buttons').click(function (){
          $body.addClass("loading");
          $('#input_try_id').val($(this).data("id"));
          $('#try_id_report').submit();
        });
        $('.cmp_buttons').click(function (){
          $body.addClass("loading");
          $('#input_cmp_id').val($(this).data("id"));

          $('#filter_form').submit();
        });
        $('.del_buttons').click(function (){
          $('#input_del_try_id').val($(this).data("id"));
          var isAdmin = confirm("Try ID " + $(this).data("id") +" will be deleted with reports");
            
           if (isAdmin == true)       
              $('#del_try_id').submit();
        });
        $('#export_calls_btn').click(function (){
          
          $('#export_calls_form').submit();
        });

       

        $(document).on({
            ajaxStart: function() { $body.addClass("loading");    },
             ajaxStop: function() { $body.removeClass("loading"); }    
        });
    </script>