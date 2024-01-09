
    let customerEmail = jQuery('#customer-email').text();
    let conversationSubject = jQuery('#conversation-subject').text();
    let timeNow = jQuery('#time-now').text();
    let dueDate = jQuery('#due-date-13').text();
    let taskAssignee = jQuery(".nav-user").text();
    let taskDescription_n = jQuery(".thread-type-customer:last-child .thread-body").text();
    let taskDescription = taskDescription_n.replace(/\s\s+/g, " ");
    let taskUrl = window.location.href; 
    jQuery("#button-clikup").click(function(){
    let clientId = jQuery("#clients-list").find(":selected").val();
    $.ajax({
      url: "https://dev-valet.pantheonsite.io/clikup-fs-api/clickup-api-create-task.php",
      type: "POST",
      data: { customerEmail: customerEmail, conversationSubject: conversationSubject, taskAssignee: taskAssignee, clientId: clientId, timeNow: timeNow, dueDate: dueDate, taskDescription: taskDescription, taskUrl: taskUrl },
      success: function(decode_response) {
        alert("Task created! New task link is: https://app.clickup.com/t/" + decode_response);        
      }
    });
  })




 jQuery("#clients-list").change(function(){
    let clientId = jQuery("#clients-list").find(":selected").val();
    $.ajax({
      url: "https://dev-valet.pantheonsite.io/clikup-fs-api/clickup-api-get-tasks.php",
      type: "GET",
      data: { clientId: clientId },
      success: function(response) {
      $("#list-tasks").html("").append(response);
      }
    });
  })   