<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h1 class="h2">Companies</h1>
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

    <div id="sendfax_block" style="display: none;">
    <form id ="sendfax_form" action="http://192.168.1.199:9090/sendfax" enctype="multipart/form-data" method="POST">
      <input type="hidden" name ="num" id="number_input">
      <input type="hidden" name ="force_g711" id="number_input" value="1">
      <input type="file" name ="num" id="faxfile_input" >
    </form>
    </div>

    <div class="row">
      <div class="col-1">
        <table>
          <th>Channels</th>
          <th>Trunk</th>
          {foreach $online_calls as $online_call}
          
          <td>$online_call.channels</td><td>$online_call.trunk</td>
          {/foreach}

        </table>
      </div>

    </div>

    <div class="input-group">
      <fieldset class=""  >
          <legend>filter options:</legend>
          <input form="filter_form" class="filter_group" type="checkbox" {$show_disabled_cmp} name="show_disabled_cmp" value="{$show_disabled_cmp}"> Hide disabled </input><br>
          

      </fieldset>
    </div>
    
   

      <div class="col-sm">

        <div class="row">
            <button type="button" class="btn btn-success" id="add_new_cmp">Add new</button>
            </div>
        
        <div id = "add_cmp_block" class="row" style="display: none">
          
            <form id = "add_cmp_form" enctype="multipart/form-data" method="POST" >
                <div class="form-group">
                  <label for="exampleFormControlInput1">Companie name</label>
                  <input type="text" class="form-control" name = "cmp_name" id="exampleFormControlInput1" placeholder="New bulk task">
                </div>
                <div class="form-group">
                  <label for="exampleFormControlInput1">Trunks</label>
                  <!--<input type="text" class="form-control" name = "add_trunk_prefix" id="exampleFormControlInput1" placeholder="Trunk">-->
                  <select class="form-control" name = "add_trunk_prefix" id="exampleFormControlInput1" placeholder="Trunk">
                    <option value = "06">06 - WANNA Telecom</option>
                    <option value = "08">08 - Kashif</option>
                    </select>
                </div>
                <div class="form-group">
                  <label for="exampleFormControlInput1">T38: 0 / G711: 1 (0 - preferred)</label>
                  <input type="text" class="form-control" name = "add_force_g711" id="exampleFormControlInput1" placeholder="T38: 0 / G711: 1" value = "0">
                </div>
                <div class="form-group">
                  <label for="filenumbersarea">File with numebrs</label>
                  <input type="file" class="form-control-file" name = "filenums" id="filenumbersarea" ></textarea>
                </div>
                <div class="form-group">
                    <label for="filefaxarea">File with fax</label>
                    <input type="file" class="form-control-file" name = "filefax" id="filefaxarea" ></textarea>
                  </div>
                  <div class="form-group">
                    <input type="hidden" name="post_action" value="add_new_cmp"></input>
                    <button type="button" class="btn btn-success" data-action="add_new_cmp" id="btn_submit_new_cmp">Submit</button> 
                </div>
              </form>
        </div>

        <div class="row">
          <form id = "default" method="POST"></form>
        <form method="POST" id="form_show_numbers" action="index.php?main_page=numbers"></form>          
        <table class="table table-bordered table-sm table-striped table-responsive text-center" >
          <thead class="table-primary">
            <th>id</th>
            <th>Action</th>
            <th>trunk prefix</th>
            <th>G711 On\Off</th>
            <th>date</th>
            <th>Name</th>
            <th>fax</th>
            <th>count numbers</th>
            <th>status</th>
            <th>Disabled</th>

          </thead>
          
                {foreach $cmp_table as $cmp}
                <tr {if $cmp.started == "STARTED" } style="background-color: rgb(170, 247, 170);" {/if}>
                   
                    <td>{$cmp[0]}</td>
                    <td>
                      <button type="button" class="btn btn-outline-secondary font-weight-bold text-danger delete_buttons" id="delete_btn" data-action="delete" data-id="{$cmp[0]}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                      </svg>
                    </button>
                      <button type="button" class="btn btn-outline-secondary font-weight-bold text-success start_btn" id="start_btn" data-action="start_cmp" data-id="{$cmp[0]}">
                        <!--{$cmp.started}-->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-play" viewBox="0 0 16 16">
                          <path d="M10.804 8 5 4.633v6.734L10.804 8zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C4.713 12.69 4 12.345 4 11.692V4.308c0-.653.713-.998 1.233-.696l6.363 3.692z"/>
                        </svg>
                      </button>
                      <button type="button" class="btn btn-outline-secondary font-weight-bold text-dark stop_btn" id="stop_btn" data-action="stop_cmp" data-id="{$cmp[0]}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-stop-circle" viewBox="0 0 16 16">
                          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                          <path d="M5 6.5A1.5 1.5 0 0 1 6.5 5h3A1.5 1.5 0 0 1 11 6.5v3A1.5 1.5 0 0 1 9.5 11h-3A1.5 1.5 0 0 1 5 9.5v-3z"/>
                        </svg>
                      </button>
                    </td>
                    <td class = prefix_fields data-cmp_id = "{$cmp[0]}" >
                        <div class = "fields d-block">{$cmp[7]}</div>
                        <div class = "fields d-none" input_cmp_id = "{$cmp[0]}">
                          <form action="" method=POST>
                            <button type=submit form="default">CANCEL</button>
                          <input type=hidden name="cmp_id" value="{$cmp[0]}"/> 
                          <input type=hidden name="post_action" value="set_prefix"/>
                          <input class="input form-control" type = text name="input_set_prefix"/>
                          <button type=submit >OK</button>
                        </form>
                      </div>
                      </td>
                      <td class = g711_fields data-cmp_id = "{$cmp[0]}" >
                        <div class = "fields d-block">{$cmp[8]}</div>
                        <div class = "fields d-none" input2_cmp_id = "{$cmp[0]}">
                          <form action="" method=POST>
                          <button type=submit form="default">CANCEL</button>
                          <input type=hidden name="cmp_id" value="{$cmp[0]}"/> 
                          <input type=hidden name="post_action" value="set_g711"/>
                          <input class="input form-control" type = text name="input_set_g711"/>
                          <button type=submit >OK</button>
                        </form>
                      </div>
                      </td>

                    <td>{$cmp[6]}</td>
                    <td>{$cmp[1]}</td>
                    <td>{$cmp[2]}</td>
                    <td><button id="input_num" type="submit" class="btn-link btn" form="form_show_numbers" name="filter_cmp" value="{$cmp[0]}">{$cmp.sent} / {$cmp[3]}</button></td><td >{$cmp[4]} </td>
                    <td>
                        <input type="checkbox" class="disable-check" data-action="disable-enable" {$cmp[5]} data-id="{$cmp[0]}">
                        
                    </td>
                   

                 </tr>
                {/foreach}
                
        </table>
        </div>

      </div>
      
      
    </div>
   
  </main>
  <script>
      $('#add_new_cmp').click(function(){
          $('#add_cmp_block').toggle();

      }
      );

      $('#btn_submit_new_cmp').click(function(){
        
         $('#add_cmp_form').submit();
        // window.location.reload();
      }
      
      );

      $('.delete_buttons').click( function() {
        var isAdmin = confirm("Companie " + $(this).data("id") +" will be deleted with uploaded numbers");
        console.log($(this).data("id") + " " + $(this).data("action"));
        if (isAdmin == true)             
            $.post("index.php?main_page=companies",  { post_action: $(this).data("action"), post_id: $(this).data("id") } ).done(function(data){
                console.log(data);
            

        });
        location.reload();
       });
       
       $('.disable-check').change( function() {
        //$(this).toggle();
        
        console.log($(this).data("id") + " " + $(this).data("action") + " " + $(this).is( ":checked" ));

        $.post("index.php?main_page=companies",  { post_action: $(this).data("action"), post_id: $(this).data("id") , post_data: $(this).is( ":checked" )} ).done(function(data){
            console.log(data);
        });
        location.reload();
       });

       $('.start_btn').click( function() {
        
        console.log($(this).data("id") + " " + $(this).data("action") );
        console.log($(this).text() + " " + $(this).data("action") + $(this).data("id") );



        var button = $(this);
        $.post("cmp_manage.php",
                { post_action: $(this).data("action"), cmp_id: $(this).data("id") }

                );
                //curl -F "post_action=start_cmp" -F "cmp_id=223" cmp_manage.php
              /*.done(function(data) {
            console.log(data);
            button.text("START");
            button.attr("disabled", false); 
            
          
            
            
        });
        */

        button.text("STARTED");
        button.attr("disabled", true); 
        //$(this).toggle(); 
        //$(this).removeClass("btn-success");
        //$(this).addClass("btn-black");
        location.reload();
       });

       $('.stop_btn').click( function() {
        
        //console.log($(this).data("id") + " " + $(this).data("action") );
        //console.log($(this).text() + " " + $(this).data("action") + $(this).data("id") );
        


        var button = $(this);
        $.post("cmp_manage.php",  { post_action: $(this).data("action"), cmp_id: $(this).data("id") } ).done(function(data){
            console.log(data);
            //button.text("START");
            //button.attr("disabled", false); 
            //$('#start_btn').addClass("btn-success");
            //$('#start_btn').removeClass("btn-black");
        });

//        $(this).text("STOP");
        //$(this).toggle(); 
  //      $(this).removeClass("btn-success");
    //    $(this).addClass("btn-black");
        location.reload();
       });
  //     $('#show_numbers').onclick(function(data) {
    //     $('form_show_numbers').submit();
      // });

    $('.prefix_fields').click(function() {
      console.log($(this).data("cmp_id"));
      
      var edit_field = $("div[input_cmp_id|='" + $(this).data("cmp_id") + "']");
      
      if (edit_field.hasClass('d-none')) {
        edit_field.addClass('d-inline');
      }
      
      
      //$("a[input_cmp_id|='246']").addClass('d-block');      
  /*    
      $.post("cmp_manage.php",  { post_action: "update_prefixes", $(this).data("prefixes"), cmp_id: $(this).data("id") } ).done(function(data){
            console.log(data);
        });
*/
      });
      $('.g711_fields').click(function() {
      console.log($(this).data("cmp_id"));
      
      var edit_field = $("div[input2_cmp_id|='" + $(this).data("cmp_id") + "']");
      
      if (edit_field.hasClass('d-none')) {
        edit_field.toggleClass('d-none');
      }
    });
      
   
       
  </script>