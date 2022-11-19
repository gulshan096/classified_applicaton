
	function showresponse(showtype,showmessage,targetelement)
		{
			$(targetelement).fadeIn("slow");
				$(targetelement).html(showmessage);
					
					$(targetelement).addClass("alert");
					$(targetelement).removeClass("alert-success");
					$(targetelement).removeClass("alert-warning");
					$(targetelement).removeClass("alert-danger");
					$(targetelement).removeClass("alert-info");
					
					switch(showtype)
						{
							case 1:
									$(targetelement).addClass("alert-success");
							break;
							case 2:
									$(targetelement).addClass("alert-info");
							break;
							case 3:
									$(targetelement).addClass("alert-warning");
							break;
							default:				
									$(targetelement).addClass("alert-danger");
							break;
						}
			return "";
		}
		
		
		
				
			function dorequest(url,formclass,resclass)	
				{
				  
				   
				   
					$(resclass).html("Loading...");
						 var form = $(formclass)[0];
						 var formData = new FormData(form);
						 
							$.ajax({
									type: "POST",
									async: true,
									cache: false,
									url: url,
								
									data: formData,
									
									//processData: true,
									//contentType: true,
									
									processData: false,
									contentType: false,
									success: function (tempdata)
										{				
												if (tempdata.trim() != '') 
													{
														var values = JSON.parse(tempdata);
														showresponse(values.status,values.message,resclass);
														
														if ( typeof values.redurl !== 'undefined' && values.redurl !== '0' )
															{
																setTimeout(function(){
																	window.location.href	= values.redurl;	
																},786);
															}
														
													} else
													{
														alert("error");
														showresponse(0,"Something went wrong, Please try again laters.",resclass);
													}
										},
									cache: false
								}).fail(function (jqXHR, textStatus, error) {
								
									showresponse(0,"Something went wrong, Please try again later.",resclass);
				
								});

					return false;
				}
				
				
			
				