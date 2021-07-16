<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h1 class="h2">Numbers</h1>
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
    <form id="filter_form" method="POST"></form>
    <div class="row">
        <div class="input-group">
            <fieldset class=""  >
                <legend>Group option:</legend>
                <input form="filter_form" class="filter_group" type="checkbox" {$group_by_cmp} name="group_by_cmp"> by Companie </input><br>
                <input form="filter_form" class="filter_group" type="checkbox" {$group_by_num}  name="group_by_num"> by Number </input><br>

            </fieldset>
          </div>

          <div class="col-sm">
            

            
          </div>
        <table class="table table-bordered table-sm table-striped text-center" >
        
            <thead class="table-primary">
                <th>id</th>
                <th>created</th>
                <th>Number</th>
                <th>Companie</th>
                <th>status</th>
                

            </thead>
            <tr class="table-active">
            <td><input form="filter_form" type="text" class="filter_input form-control" name="filter_id"></input></td>
            <td><input form="filter_form" type="text" class="filter_input form-control" name="filter_created"></input></td>
            <td><input form="filter_form" type="text" class="filter_input form-control" name="filter_num" value='{$filter_num}'></input></td>
            <td><input form="filter_form" type="text" class="filter_input form-control" name="filter_cmp" value='{$filter_cmp}'></input></td>
            <td><input form="filter_form" type="text" class="filter_input form-control" name="filter_status"></input></td>
            
            
        </tr>
                {foreach $num_table as $num}
                <tr>
                   
                    <td >{$num.id}</td>
                    <td>{$num.created}</td>
                    <td>{$num.number}</td>
                    <td >{$num.cmp_name}(id:{$num.companieid})</td>
                    <td >{$num.status}</td>
                   
                    
                 </tr>
                {/foreach}
                
        </table>
        </div>
    </main>
    <script>
        $(".filter_input").on('keypress',function(data) {
             if(data.which == 13) {
                alert("You pressed enter! " + $(this).val() + " " + $(this).data("action") + " " + $(this).is( ":checked" ));
                $('#filter_form').submit();
            }
        });
        
        $(".filter_group").change(function(data) {
            $('#filter_form').submit();
            
        });
    
    </script>