$(document).ready(function(){
    var relPos = "../lodspeakr/components/";
    var currentXhr = null;    
    var templateBuffer = '';
    var queryBuffer = '';
    CodeMirror.defineMode('mustache', function(config, parserConfig) {
  var mustacheOverlay = {
    token: function(stream, state) {
      var ch;
      if (stream.match('{{')) {
        while ((ch = stream.next()) != null)
          if (ch == '}' && stream.next() == '}') break;
        stream.eat('}');
        return 'mustache';
      }
      while (stream.next() != null && !stream.match('{{', false)) {}
      return null;
    }
  };
  return CodeMirror.overlayMode(CodeMirror.getMode(config, parserConfig.backdrop || 'text/html'), mustacheOverlay);
});


    //Create Template and Query Editor
    var templateEditor = CodeMirror.fromTextArea(document.getElementById('template-editor'), {mode: 'mustache',
    onChange:function(e){
     if(templateEditor.getValue() == templateBuffer){
       $('#template-save-button').addClass('disabled');
     }else{
       $('#template-save-button').removeClass('disabled');
     }
     }
     });
     var queryEditor = CodeMirror.fromTextArea(document.getElementById('query-editor'), {mode: 'sparql',
    onChange:function(e){
     if(queryEditor.getValue() == queryBuffer){
       $('#query-save-button').addClass('disabled');
     }else{
       $('#query-save-button').removeClass('disabled');
     }
     }

    });

    var tip = $('<div></div>');
        tip.css("width", "400px")
           .css("height", "220px")
           .css("font", "Monaco")
           .css("font-size", "13px")
           .css("text-wrap", "unrestricted")
           .css("position", "absolute")
           .css("z-index", "100")
           .css("background","white")
           .css("border", "1px solid black")
           .css("-moz-border-radius", "8px 8px")
           .css("border-radius", "8px 8px")
           .css("font-family", "Helvetica Neue")
           .css("color", "black")
           .css("padding", "5px")
           .css("opacity", 0);
    $("body").append(tip);
    var cheatSheet = $("body").append("<div id='cheat' class='cheat-sheet first-editor'></div>");
    $("#cheat").on('mouseenter', function(){$(this).animate({right: '+=200'}, 180)})
    .on('mouseleave', function(){$(this).animate({right: '-=200'}, 180);tip.css("top", 1000).css("left", 0)}).html("<span class='cheat-title'>Visualization Filters</span>")
    .on('mousemove', function(e){tip.css("top", e.pageY-100).css("left", e.pageX-430)});
    
    $("#cheat").append("<ul id='cheat-list' class='cheat-list'></ul>");
    
    var visualizationsEnabled = [ {name: 'D3CirclePacking', params: "childNode,parentNode", img: "circlepacking.png"}, 
      {name: 'D3Dendrogram',  params: "childNode,parentNode", img: "dendrogram.png"},
      {name: 'D3ForceGraph',  params: "sourceNode,TargetNode", img: "graph.png"},
      {name: 'D3ParallelCoordinates',  params: "label,value1,value2,...,valueN", img: "parallelcoordinates.png"},
      {name: 'D3WordCloud', params: "textVariable", img: "wordcloud.png"},
      {name: 'GoogleMaps',  params: "latitude,longitude,label", img: "maps.png"},
      {name: 'GoogleVizBarChart', params: "valuesInAxisX,valuesInAxisY", img: "barchart.png"},
      {name: 'GoogleVizColumnChart', params: "valuesInAxisX,valuesInAxisY", img: "columnchart.png"},
      {name: 'GoogleVizLineChart', params: "valuesInAxisX,valuesInAxisY", img: "linechart.png"},
      {name: 'GoogleVizPieChart', params: "valuesInAxisX,valuesInAxisY", img: "piechart.png"},
      {name: 'GoogleVizScatterChart', params: "valuesInAxisX,valuesInAxisY", img: "piechart.png"},
      {name: 'GoogleVizTable', params: "column1,column2,...,columnN", img: "table.png"},
    {name: 'Timeknot', params: "date,label[,image]", img: "timeknots.png"}];
    $.each(visualizationsEnabled, function(index, value){
        $("#cheat-list").append("<li><a href='#' class='cheat-link' data-params='"+value.params+"' data-img='"+value.img+"'>"+value.name+"</a></li>");
    });
    
    $(".cheat-link").on('click', function(){
        var visualFilter = '{{models.main|'+$(this).html()+':"'+$(this).attr("data-params")+'"}}';
        templateEditor.replaceSelection(visualFilter);
    })
    .on('mouseenter', function(){var visualFilter = '<h3>'+$(this).html()+'</h3><p style="background:#ccc">{{models.main|'+$(this).html()+':"'+$(this).attr("data-params")+'"}}</p><img src="img/'+$(this).attr("data-img")+'"/>';tip.html(visualFilter).animate({opacity: .95}, 20)})
    .on('mouseleave', function(){tip.animate({opacity: '0'}, 20)});

 var sparqlSheet = $("body").append("<div id='sparql' class='cheat-sheet second-editor'></div>").css("top:520px");
    $("#sparql").on('mouseenter', function(){$(this).animate({right: '+=200'}, 180)})
    .on('mouseleave', function(){$(this).animate({right: '-=200'}, 180);tip.css("top", 1000).css("left", 0)}).html("<span class='cheat-title'>SPARQL Queries</span>")
    .on('mousemove', function(e){tip.css("top", e.pageY-100).css("left", e.pageX-430)});
       
    $("#sparql").append("<ul id='sparql-list' class='cheat-list'></ul>");
    
    
    var sparqlQueriesEnabled = [ {name: 'Get classes', query: "SELECT DISTINCT ?class WHERE {\n [] a ?class\n}\n#You can change the limit to obtain more results\nLIMIT 100"}, 
      {name: 'Get instances of a class',  query: "SELECT DISTINCT ?instance WHERE{\n  ?instance a <http://my/class>\n}\n#You can change the limit to obtain more results\nLIMIT 100"},
      {name: 'Get properties of a resource', query: "SELECT DISTINCT ?property ?object WHERE{\n  <http://my/resource> ?property ?object\n}\n#You can change the limit to obtain more results\nLIMIT 100"},
       {name: 'All properties pointing to a resource', query: "SELECT DISTINCT ?subject ?object WHERE{\n ?subject ?property <http://my/resource>\n}\n#You can change the limit to obtain more results\nLIMIT 100"}];

                                  
    $.each(sparqlQueriesEnabled, function(index, value){
      $("#sparql-list").append("<li><a href='#query-editor' class='sparql-link' data-query='"+value.query+"'>"+value.name+"</a></li>");
    });
    $(".sparql-link").on('click', function(){
        queryEditor.setValue($(this).attr('data-query'));
    })
                              
    


    //Test button
    $("#query-test-button").on('click', function(e){
      $("#results").empty();
      if($(this).hasClass("btn-success")){
        var query = queryEditor.getValue();
        var endpoint = $("#endpoint-list>option:selected").val();
        $("#query-test-button").removeClass('btn-success').html('<img src="../img/wait.gif"/> Stop');
        currentXhr = executeQuery(query, endpoint);
      }else{
        currentXhr.abort();
        $(this).addClass('btn-success').html('Test this query against');
      }
    });

    //X button on components
    $(".component-li").on({
        mouseenter: function(){
          $(this).children(".lodspk-delete-component").removeClass("hide");
        },
        mouseleave: function(){
          $(".lodspk-delete-component").addClass("hide");
        }
    });   

    function updateEmbeddableCode(e){
      var url = $("#embed-button").attr('data-url').replace("../", home);
      var width = $("#embed-width").val(),
          height = $("#embed-height").val();
      var code = "&lt;iframe src='"+url+"' style='overflow-x: hidden;overflow-y: hidden;' frameborder='0' width='"+width+"' height='"+height+"'&gt;&lt;/iframe&gt;";
      $("#embed-body").html(code);
      $("#embed-box").modal('show');
    }   
    
 function executePost(url, data, message){
      $.ajax({
         type: 'POST',
         url: url,
         data: data,
         success: function(data){if(data.success == true){
           $(message.id).removeClass('hide').addClass('alert-success').html(message.success).show().delay(2000).fadeOut("slow").removeClass('alert-success');
           if(message.triggerElement != undefined && message.triggerEvent != undefined ){
             $(message.triggerElement).trigger(message.triggerEvent);
           }else{
             setTimeout(window.location.reload(), 2000);
           }
         }else{
           $(message.id).removeClass('hide').addClass('alert-error').html(message.failure).show().delay(2000).fadeOut("slow").removeClass('alert-error');         
         }
         },
         error: function(data){
           $(message.id).removeClass('hide').addClass('alert-error').html(message.error).show().delay(2000).fadeOut("slow").removeClass('alert-error');
         },
         dataType: 'json'
     });
 }   
    
 $(".new-button").on("click", function(e){
     var componentName = prompt("Please enter the name of the new component","newComponent");
     if(componentName != null){
       var url   = "components/create/"+$(this).attr("data-type")+"/"+componentName;
       var data  = {content: $("#template-editor").val()};
       var msgId = "#component-msg";
       executePost(url, data, {id:msgId, success: "Saved!", failure: "Can't create new component. Probably permissions problem or component already exists", error: "Error creating a new component!"});
     }
 });

  //New file
  $(".new-file-button").on("click", function(e){
     var fileName = "";
     if($(this).hasClass("new-file-button-view")){
       fileName = prompt("Please enter the name of the new view","json");
       
       if(! /[^\/\.\s]+$/g.test(fileName)){
         alert("File name is not valid. It can't have spaces or .");
         return;
       }
       fileName = fileName+".template";
     }else{
       fileName = prompt("Please enter the name of the new model","newModel");
       if(! /^([^\/\s]+\/)*[^\/\s]+$/.test(fileName)){
         alert("File name is not valid. Format is [ENDPOINTPREFIX/]*FILENAME");
         return;
       }
       var filePath = fileName.split("/");
       lastFilePath = filePath.pop();
       lastFilePath += ".query";
       filePath = filePath.map(function(d){return "endpoint."+d});
       fileName = filePath.join("/")+"/"+lastFilePath;
     }
     if(fileName != null){
       var url   = "components/add/"+$(this).attr("data-component")+"/"+fileName;
       var data  = {content: $("#template-editor").val()};
       var msgId = "#component-msg";
       var comp = $(this).attr("data-component").split("/");
       executePost(url, data, {id:msgId, success: "Saved!", failure: "Can't create new file. Probably permissions problem or file already exists", error: "Error creating a new file!", triggerElement: "[data-component-type="+comp[0]+"][data-component-name="+comp[1]+"]" , triggerEvent: 'click'});
     }
  });
  
  $(".lodspk-delete-component").on({
      click: function(){
        var componentName = $(this).attr("data-component-name");
        var componentType = $(this).attr("data-component-type");
        var url = "components/delete/"+componentType+"/"+componentName;
       var msgId = "#component-msg";
        if (confirm("Are you sure you want to delete this component?")) {
          executePost(url, "", {id:msgId, success: "Component deleted!", failure: "Can't delete component. Probably permissions problem", error: "Error deleting component!"});      
        }
      }
  });
  
  
 
 $(".lodspk-component").on("click", function(e){
     $(".lodspk-component").removeClass("strong");
     $(this).addClass("strong");
     var componentType = $(this).attr("data-component-type");
     var componentName = $(this).attr("data-component-name");
     var dataParent = ".lodspk-component[data-component-type="+componentType+"][data-component-name="+componentName+"]";
     var url="components/details/"+componentType+"/"+componentName;
     templateBuffer = "";
     queryBuffer = "";
     templateEditor.setValue("");
     queryEditor.setValue("");
  $.get(url, function(data){
      $("#template-list").empty();
      $("#query-list").empty();
      $("#preview-button").attr("target", "_new").attr("href", "../"+componentName).removeClass("hide");
      $("#embed-button").attr("data-url", "../"+componentName).removeClass("hide");
      $.each(data.views, function(i, item){
          var viewUrl = relPos+componentType+"/"+componentName+"/"+item;
          var viewFileUrl = componentType+"/"+componentName+"/"+item;
          var displayName = item.replace(".template","");
          $("#template-list").append("<li class='file-li'><button type='button' class='close hide lodspk-delete-file' data-parent='"+dataParent+"' data-file='"+viewFileUrl+"' style='align:left'>x</button><a class='lodspk-template' href='#template-editor' data-url='"+viewUrl+"'>"+displayName+"</a></li>") ;
      });
      $.each(data.models, function(i, item){
          var modelUrl = relPos+componentType+"/"+componentName+"/queries/"+item;
          var modelFileUrl = componentType+"/"+componentName+"/queries/"+item;
          var displayName = item.split("/").map(function(d){return d.replace("endpoint.","").replace(".query", "")}).join("/");
          $("#query-list").append("<li class='file-li'><button type='button' class='close hide lodspk-delete-file' data-parent='"+dataParent+"' data-file='"+modelFileUrl+"' style='align:left'>x</button><a href='#' class='lodspk-query' data-url='"+modelUrl+"'>"+displayName+"</a></li>");
          $('html, body').stop().animate({
                      scrollTop: $('#template-list').offset().top - 100
                    }, 500);
      });
      updateEvents();
      $(".new-file-button").removeClass("hide");
      $(".new-file-button-view").attr("data-component", componentType+"/"+componentName );
      $(".new-file-button-model").attr("data-component", componentType+"/"+componentName+"/queries" );
  });
 });
 
 function executeQuery(q, e){
   return $.ajax({
       dataType: 'jsonp',
       data: {
         query: q,
         format: 'application/sparql-results+json'
       },
       url: e,
       success: function(data){
         var variables = new Array();
         var header = $("<tr></tr>");
         $(data.head.vars).each(function(i, item){
             variables.push(item);
             header.append("<td><strong>"+item+"</strong></td>");
         });
         $("#results").append(header);
         $(data.results.bindings).each(function(i, item){
             var row = $("<tr></tr>");
             $.each(variables, function(j, jtem){
                 var value = "";
                 if(item[jtem] != undefined){
                   value = item[jtem].value;
                 }
                 row.append("<td>"+value+"</td>");
             });
             $("#results").append(row);
         });
         $("#query-test-button").addClass('btn-success').html('Test this query against');
       },
       error: function(e){
         $("#results-msg").html("An error occurred when sending a query to the endpoint").show().delay(2000).fadeOut("slow");
         $("#query-test-button").addClass('btn-success').html('Test this query against');
       },
       timeout: 20000,
   });
 }
 
 function updateEvents(){
   $(".lodspk-delete-file").on({
       click: function(){
         var fileName = $(this).attr("data-file");
         var url = "components/remove/"+fileName;
         var msgId = "#component-msg";
         if (confirm("Are you sure you want to delete this component?")) {
           executePost(url, "", {id:msgId, success: "File deleted!", failure: "Can't delete file. Probably permissions problem", error: "Error deleting file!", triggerElement:  $(this).attr("data-parent"), triggerEvent: 'click'});      
         }
       }
   });
   

   $(".file-li").on({
       mouseenter: function(){
         $(this).children(".lodspk-delete-file").removeClass("hide");
       },
       mouseleave: function(){
         $(".lodspk-delete-file").addClass("hide");
       }
   });   
   $(".lodspk-template").on("click", function(e){
     $(".lodspk-template").removeClass("strong");
     $(this).addClass("strong");
       var fileUrl = $(this).attr("data-url");
       $.ajax({
           cache: false,
           url: fileUrl, 
           dataType: 'text',
           success: function(data){
           templateEditor.setValue(data);
           templateBuffer = data;
           $("#template-save-button").attr("data-url", fileUrl).addClass("disabled");
           $('html, body').stop().animate({
                      scrollTop: $('body').offset().top-100
                    }, 500);
       }
       });
   });
   $(".lodspk-query").on("click", function(e){
     $(".lodspk-query").removeClass("strong");
     $(this).addClass("strong");
       var fileUrl = $(this).attr("data-url");
       $.ajax({
           cache: false,
           url: fileUrl, 
           dataType: 'text',
           success: function(data){
           queryEditor.setValue(data);
           queryBuffer = data;
           $("#query-save-button").attr("data-url", fileUrl).addClass("disabled");
           $('html, body').stop().animate({
                      scrollTop: $('.bs-docs-query').offset().top-100
                    }, 100);
       }
       });
   });
   //Turn 'save' buttons disable when no change has been made
   /*$("#query-editor").on("keyup", function(e){
     if($("#query-editor").val() == queryBuffer){
       $("#query-save-button").addClass("disabled");
     }else{
       $("#query-save-button").removeClass("disabled");     
     }
   });
   templateEditor.("keyup", function(e){
     if($("#template-editor").val() == templateBuffer){
       $("#template-save-button").addClass("disabled");
     }else{
       $("#template-save-button").removeClass("disabled");     
     }
   });*/ 
   //Save action
   $("#template-save-button").on("click", function(e){
       if(!$("#template-save-button").hasClass("disabled")){
         var url = "components/save/"+$("#template-save-button").attr("data-url").replace(relPos, "");
         $.ajax({
             type: 'POST',
             url: url,
             data: {content: templateEditor.getValue()},
             success: function(data){if(data.success == true){
               $("#template-msg").removeClass('hide').html("Saved!").show().delay(2000).fadeOut("slow");
             }},
             dataType: 'json'
         });
         
         templateBuffer=templateEditor.getValue();
         $("#template-save-button").addClass('disabled');
       }
   });
   $("#query-save-button").on("click", function(e){
       if(!$("#query-save-button").hasClass("disabled")){
         var url = "components/save/"+$("#query-save-button").attr("data-url").replace(relPos, "");
         $.ajax({
             type: 'POST',
             url: url,
             data: {content: queryEditor.getValue()},
             success: function(data){if(data.success == true){
               $("#query-msg").removeClass('hide').html("Saved!").show().delay(2000).fadeOut("slow");
             }else{
               $("#query-msg").removeClass('hide').addClass('alert-fail').html("Error saving content").show().delay(2000).fadeOut("slow").removeClass('alert-fail');             
             }
             },
             error: function(data){if(data.success == true){
               $("#query-msg").removeClass('hide').addClass('alert-fail').html("An error ocurred in the server!").show().delay(2000).fadeOut("slow").removeClass('alert-fail');
             }},
             dataType: 'json'
         });
         
         queryBuffer=queryEditor.getValue();
         $("#query-save-button").addClass('disabled');
       }
   });
   
   $("#embed-button").on('click', updateEmbeddableCode);
   $(".embed-size").on('keyup', updateEmbeddableCode);
   
 }
});
