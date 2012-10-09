$(document).ready(function(){
    var relPos = "../lodspeakr/components/";
    var templateBuffer = "";
    var queryBuffer = "";
    $(".component-li").on({
        mouseenter: function(){
          $(this).children(".lodspk-delete-component").removeClass("hide");
        },
        mouseleave: function(){
          $(".lodspk-delete-component").addClass("hide");
        }
    });   
    $(".lodspk-delete-component").on({
        click: function(){
          var componentName = $(this).attr("data-component-name");
          var componentType = $(this).attr("data-component-type");
          var url = "components/delete/"+componentType+"/"+componentName;
          if (confirm("Are you sure you want to delete this component?")) {
            $.ajax({
                type: 'POST',
                url: url,
                data: {content: $("#template-editor").val()},
                success: function(data){if(data.success == true){
                  $("#component-msg").removeClass('hide').addClass('alert-success').html("Component removed!").show().delay(2000).fadeOut("slow").removeClass('alert-success');
                  setTimeout(window.location.reload(), 2000);
                }},
                error: function(data){
                  $("#component-msg").removeClass('hide').addClass('alert-error').html("Error removing component!").show().delay(2000).fadeOut("slow").removeClass('alert-error');
                },
                dataType: 'json'
            });
          }
        }
    });

 $(".new-button").on("click", function(e){
     var componentName = prompt("Please enter the name of the new component","newComponent");
     var url = "components/create/"+$(this).attr("data-type")+"/"+componentName;
     $.ajax({
         type: 'POST',
         url: url,
         data: {content: $("#template-editor").val()},
         success: function(data){if(data.success == true){
           $("#component-msg").removeClass('hide').addClass('alert-success').html("Saved!").show().delay(2000).fadeOut("slow").removeClass('alert-success');
           setTimeout(window.location.reload(), 2000);
         }},
         error: function(data){
           $("#component-msg").removeClass('hide').addClass('alert-error').html("Error creating a new service!").show().delay(2000).fadeOut("slow").removeClass('alert-error');
         },
         dataType: 'json'
     });

 });
 $(".lodspk-component").on("click", function(e){
     var componentType = $(this).attr("data-component-type");
     var componentName = $(this).attr("data-component-name");
     var url="components/details/"+componentType+"/"+componentName;
     templateBuffer = "";
     queryBuffer = "";
     $("#template-editor").val("");
     $("#query-editor").val("");
  $.get(url, function(data){
      $("#template-list").empty()
      $("#query-list").empty()
      $.each(data.views, function(i, item){
          var viewUrl = relPos+componentType+"/"+componentName+"/"+item;
          $("#template-list").append("<li><a class='lodspk-template' href='#template-save-button' data-url='"+viewUrl+"'>"+item+"</a></li>") ;
      });
      $.each(data.models, function(i, item){
          var modelUrl = relPos+componentType+"/"+componentName+"/queries/"+item;
          $("#query-list").append("<li><a href='#query-save-button' class='lodspk-query' data-url='"+modelUrl+"'>"+item+"</a></li>")        
      });
      updateEvents();
  });
 });
 function updateEvents(){
   $(".lodspk-template").on("click", function(e){
       var fileUrl = $(this).attr("data-url");
       $.ajax({
           cache: false,
           url: fileUrl, 
           success: function(data){
           $("#template-editor").val(data);
           templateBuffer = data;
           $("#template-save-button").attr("data-url", fileUrl).addClass("disabled");
       }
       });
   });
   $(".lodspk-query").on("click", function(e){
       var fileUrl = $(this).attr("data-url");
       $.ajax({
           cache: false,
           url: fileUrl, 
           success: function(data){
           $("#query-editor").val(data);
           queryBuffer = data;
           $("#query-save-button").attr("data-url", fileUrl).addClass("disabled");
       }
       });
   });
   //Turn 'save' buttons disable when no change has been made
   $("#query-editor").on("keyup", function(e){
     if($("#query-editor").val() == queryBuffer){
       $("#query-save-button").addClass("disabled");
     }else{
       $("#query-save-button").removeClass("disabled");     
     }
   });
   $("#template-editor").on("keyup", function(e){
     if($("#template-editor").val() == templateBuffer){
       $("#template-save-button").addClass("disabled");
     }else{
       $("#template-save-button").removeClass("disabled");     
     }
   }); 
   //Save action
   $("#template-save-button").on("click", function(e){
       if(!$("#template-save-button").hasClass("disabled")){
         var url = "components/save/"+$("#template-save-button").attr("data-url").replace(relPos, "");
         $.ajax({
             type: 'POST',
             url: url,
             data: {content: $("#template-editor").val()},
             success: function(data){if(data.success == true){
               $("#template-msg").removeClass('hide').html("Saved!").show().delay(2000).fadeOut("slow");
             }},
             dataType: 'json'
         });
         
         templateBuffer=$("#template-save-button").val();
         $("#template-save-button").addClass('disabled');
       }
   });
   $("#query-save-button").on("click", function(e){
       if(!$("#query-save-button").hasClass("disabled")){
         var url = "components/save/"+$("#query-save-button").attr("data-url").replace(relPos, "");
         $.ajax({
             type: 'POST',
             url: url,
             data: {content: $("#query-editor").val()},
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
         
         queryBuffer=$("#query-save-button").val();
         $("#query-save-button").addClass('disabled');
       }
   });
 }
});
