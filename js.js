var tags = [];
var index = 0;
var layer = 0;
var latitude = 0;
var longitude = 0;
var map;
var notifs = [];
var audio = new Audio('/sounds/notification.mp3');
var ip = 0;
var got = 0;
var limit = 0;
var file_0;
var file_1;
var file_2;
var file_3;
var file_4;
var msg = [];

function sendMsg(key, id)
{
	if (event.keyCode == 13 && $('#msg').val() != "" && id != 'unknown')
	{
		$.ajax(
		{
			url : '/add_message.php',
			type : 'POST',
			data : 'msg=' + $('#msg').val() + '&id=' + id,
			dataType : 'html',
			success : function(code_html, statut)
			{
				if (code_html != "log" && code_html != "error")
				{
					$('#msg').val("");
					var arr = JSON.parse(code_html);
					msg.push(arr[0]);
					$("#chat_box").append(arr[1]);
				}
			}
		});
	}
}
function getMsg(id)
{
	$.ajax(
	{
		url : '/get_messages.php',
		type : 'POST',
		data : 'id=' + id,
		dataType : 'html',
		success : function(code_html, statut)
		{
			if (code_html != "log" && code_html != "error")
			{
				var arr = JSON.parse(code_html);
				for (var i = 0; i < arr.length; i++)
				{
					if (!in_array(msg, arr[i][0]))
					{
						msg.push(arr[i][0]);
						$("#chat_box").append(arr[i][1]);
					}
				}
			}
		}
	});
}
async function startMsg(id)
{
	if (id == "unknown")
		return ;
	while (1)
	{
		getMsg(id);
		await sleep(1000);
	}
}
function loadMore()
{
	if (got == 0)
	{
		loadMore();
		return ;
	}
	var url = '/get_research.php';
	if ($("#type").val() == '0')
		url = '/get_suggestions.php';
	var data = new FormData();
	data.append('age_min', $("#age_min").val());
	data.append('age_max', $("#age_max").val());
	data.append('popularity_min', $("#popularity_min").val());
	data.append('popularity_max', $("#popularity_max").val());
	data.append('latitude', latitude);
	data.append('longitude', longitude);
	data.append('distance_min', $("#distance_min").val());
	data.append('distance_max', $("#distance_max").val());
	data.append('tags', JSON.stringify(tags));
	data.append('order', $("#order").val());
	data.append('limit', limit);
	$.ajax(
	{
		url : url,
		type : 'POST',
		data : data,
		processData: false,
		contentType: false,
		dataType : 'html',
		success : function(code_html, statut)
		{
			if (code_html != "log" && code_html != "error")
			{
				$("#more").remove();
				$("#index_box").append(code_html);
			}
		}
	});
	limit += 20;
}
function getIndex()
{
	if (got == 0)
	{
		getIndex();
		return ;
	}
	limit = 0;
	var url = '/get_research.php';
	if ($("#type").val() == '0')
		url = '/get_suggestions.php';
	var data = new FormData();
	data.append('age_min', $("#age_min").val());
	data.append('age_max', $("#age_max").val());
	data.append('popularity_min', $("#popularity_min").val());
	data.append('popularity_max', $("#popularity_max").val());
	data.append('latitude', latitude);
	data.append('longitude', longitude);
	data.append('distance_min', $("#distance_min").val());
	data.append('distance_max', $("#distance_max").val());
	data.append('tags', JSON.stringify(tags));
	data.append('order', $("#order").val());
	data.append('limit', limit);
	$.ajax(
	{
		url : url,
		type : 'POST',
		data : data,
		processData: false,
		contentType: false,
		dataType : 'html',
		success : function(code_html, statut)
		{
			if (code_html != "log" && code_html != "error")
				$("#index_box").html(code_html);
		}
	});
	limit += 20;
}
function removeTagIndex(id)
{
	var text = $("#text_" + id).text();
	var text = tags.indexOf(text);
	if (text > -1)
		tags.splice(text, 1);
	$("#" + id).remove();
	getIndex();
}
function changeInputIndex(event)
{
	if (event.keyCode == 13)
	{
		var text = $('#tag').val();
		text = text.replace(/[^A-Za-z]/g, '');
		text = "#" + text;
		text = text.substring(0, 10);
		if (text.length <= 1 || tags.includes(text))
			return ;
		var div = '<div id=\"tag_' + index + '\" class=\"account_tags\">';
		div += '<img onclick=\"removeTagIndex(\'tag_' + index + '\');\" class=\"account_tags\" src=\"/imgs/grey_remove.svg\" onmouseover=\"this.src=\'/imgs/red_remove.svg\'\" onmouseout=\"this.src=\'/imgs/grey_remove.svg\'\">';
		div += '<span id=\"text_tag_' + index + '\" class=\"account_tags\">' + text + '</span>';
		div += '</div>';
		$("#box_tags").append(div);
		$('#tag').val("#");
		tags.push(text);
		index++;
		getIndex();
	}
	else
	{
		var text = $('#tag').val();
		text = text.replace(/[^A-Za-z]/g, '');
		text = "#" + text;
		text = text.substring(0, 10);
		$('#tag').val(text);
	}
}
function getPositionIndex()
{
	$.ajax(
	{
		url : '/get_position.php',
		dataType : 'html',
		success : function(code_html, statut)
		{
			if (code_html != "log" && code_html != "error")
				setMapIndex(code_html);
		}
	});
}
function setMapIndex(json)
{
	var arr = JSON.parse(json);
	if (arr[0] == "unknown" || arr[1] == "unknown" || arr[0] == "999" || arr[1] == "999")
	{
		getPositionIndex();
		return ;
	}
	latitude = arr[0];
	longitude = arr[1];
	got = 1;
	getIndex();
	mapboxgl.accessToken = 'pk.eyJ1IjoibmJydWNrZXIiLCJhIjoiY2pkZGYwYW0zMDBxazJxbXNncHR6c2JuOSJ9.gCzIaNJ4TKf6ofuQp2sRcQ';
	map = new mapboxgl.Map(
	{
		container: 'map',
		style: 'mapbox://styles/mapbox/streets-v10',
		center: [longitude, latitude],
		zoom: 13
	});
	map.addControl(new MapboxGeocoder(
	{
		accessToken: mapboxgl.accessToken
	}));
	map.on('load', function ()
	{
		map.addLayer(
		{
			"id": "symbols_" + layer,
			"type": "symbol",
			"source":
			{
				"type": "geojson",
				"data":
				{
					"type": "FeatureCollection",
					"features": [
					{
						"type": "Feature",
						"properties": {},
						"geometry":
						{
							"type": "Point",
							"coordinates": [longitude, latitude]
						}
					}]
				}
			},
			"layout":
			{
				"icon-image": "triangle-stroked-15",
				"icon-size": 2
			}
		});
		map.on('click', function (e)
		{
			latitude = e.lngLat.lat;
			longitude = e.lngLat.lng;
			got = 1;
			getIndex();
			map.removeLayer('symbols_' + layer);
			layer++;
			map.addLayer(
			{
				"id": "symbols_" + layer,
				"type": "symbol",
				"source":
				{
					"type": "geojson",
					"data":
					{
						"type": "FeatureCollection",
						"features": [
						{
							"type": "Feature",
							"properties": {},
							"geometry":
							{
								"type": "Point",
								"coordinates": [longitude, latitude]
							}
						}]
					}
				},
				"layout":
				{
					"icon-image": "triangle-stroked-15",
					"icon-size": 2
				}
			});
		});
	});
}
function getHistory()
{
	var data = new FormData();
	data.append('seen',$("#Seen").is(":checked"));
	data.append('unseen', $("#Unseen").is(":checked"));
	data.append('like', $("#Like").is(":checked"));
	data.append('unlike', $("#Unlike").is(":checked"));
	data.append('visit', $("#Visit").is(":checked"));
	data.append('block', $("#Block").is(":checked"));
	data.append('unblock', $("#Unblock").is(":checked"));
	data.append('report', $("#Report").is(":checked"));
	data.append('message', $("#Message").is(":checked"));
	data.append('limit', $("#Limit").is(":checked"));
	data.append('order', $("#Order").val());
	data.append('age', $("#age").val());
	$.ajax(
	{
		url : '/get_history.php',
		type : 'POST',
		data : data,
		processData: false,
		contentType: false,
		dataType : 'html',
		success : function(code_html, statut)
		{
			if (code_html != "error" && code_html != "log")
				$("#history_box").html(code_html);
		}
	});}
function removeAllNotifications()
{
	$.ajax(
	{
		url : '/remove_notifications.php',
		dataType : 'html',
		success : function(code_html, statut)
		{
			if (code_html != "log" && code_html != "error")
			{
				$("#notification_box").css('display', 'none');
				$("#notification_nbr").text("0");
				for (var i = 0; i < notifs.length; i++)
					$("#parent_" + notifs[i]).remove();
			}
		}
	});
}
function showNotificationBox()
{
	if ($("#notification_box").css('display') == "none")
		$("#notification_box").css('display', 'block');
	else
		$("#notification_box").css('display', 'none');
}
function sleep(ms)
{
	return new Promise(resolve => setTimeout(resolve, ms));
}
function in_array(haystack, needle)
{
	for (var i = 0; i < haystack.length; i++)
		if (haystack[i] == needle)
			return true;
	return false;
}
function in_array_0(haystack, needle)
{
	for (var i = 0; i < haystack.length; i++)
		if (haystack[i][0] == needle)
			return true;
	return false;
}
function getNotification(hey)
{
	$.ajax(
	{
		url : '/get_notifications.php',
		type : 'POST',
		data : 'ip=' + ip,
		dataType : 'html',
		success : function(code_html, statut)
		{
			if (code_html != "error" && code_html != "log")
			{
				var arr = JSON.parse(code_html);
				for (var i = 0; i < arr.length; i++)
				{
					if (!in_array(notifs, arr[i][0]))
					{
						if (hey == 0)
						{
							audio.play();
							hey = 1;
						}
						notifs.push(arr[i][0]);
						$("#notification_box").prepend(arr[i][1]);
					}
				}
				$("#notification_nbr").text(i);
				for (var i = 0; i < notifs.length; i++)
					if (!in_array_0(arr, notifs[i]))
						$("#parent_" + notifs[i]).remove();
			}
		}
	});
}
async function startWhile()
{
	getNotification(1);
	while (1)
	{
		await sleep(1000);
		getNotification(0);
	}
}
$(document).ready(function()
{
	$.get("http://ipinfo.io", function(response)
	{
		ip = response.ip;
	}, "jsonp");
	startWhile();
});
function gotoNotification(id)
{
	if ($("#child_" + id).is(":hover"))
		return ;
	window.location.href = "/notification.php?n=" + id;
}
function report(id)
{
	$.ajax(
	{
		url : '/report.php',
		type : 'POST',
		data : 'id=' + id,
		dataType : 'html',
		success : function(code_html, statut)
		{
			if (code_html == "error")
				return ;
			if (code_html == "log")
			{
				window.location.href = "signin.php";
				return ;
			}
			else if (code_html == "add")
			{
				$("#report").text("You reported this user");
				$("#report").attr("class", "profile_reported");
				$("#report").removeAttr("onclick");
				$("#report").removeAttr("id");
			}
		}
	});
}
function changeProfileImg(id)
{
	if (id != 1 && id != 2 && id != 3 && id != 4)
		return ;
	$src1 = $("#img_0").attr('src');
	$src2 = $("#img_" + id).attr('src');
	$("#img_0").attr('src', $src2);
	$("#img_" + id).attr('src', $src1);
}
function unblock(id)
{
	$.ajax(
	{
		url : '/unblock.php',
		type : 'POST',
		data : 'id=' + id,
		dataType : 'html',
		success : function(code_html, statut)
		{
			if (code_html == "error")
				return ;
			if (code_html == "log")
			{
				window.location.href = "signin.php";
				return ;
			}
			else if (code_html == "remove")
			{
				$("#" + id).remove();
			}
		}
	});
}
function block(id)
{
	$.ajax(
	{
		url : '/block.php',
		type : 'POST',
		data : 'id=' + id,
		dataType : 'html',
		success : function(code_html, statut)
		{
			if (code_html == "error")
				return ;
			if (code_html == "log")
			{
				window.location.href = "signin.php";
				return ;
			}
			else if (code_html == "add")
			{
				window.location.href = "block_list.php";
				return ;
			}
		}
	});
}
function like(id)
{
	$.ajax(
	{
		url : '/like.php',
		type : 'POST',
		data : 'id=' + id,
		dataType : 'html',
		success : function(code_html, statut)
		{
			if (code_html == "error")
				return ;
			if (code_html == "log")
			{
				window.location.href = "signin.php";
				return ;
			}
			else if (code_html == "add")
			{
				$("#like").attr('src', '/imgs/liked.svg');
				document.getElementById("popularity").textContent = parseInt(document.getElementById("popularity").textContent) + 1;
			}
			else if (code_html == "remove")
			{
				$("#like").attr('src', '/imgs/like.svg');
				document.getElementById("popularity").textContent = parseInt(document.getElementById("popularity").textContent) - 1;
			}
		}
	});
}
function getPositionProfile(id)
{
	$.ajax(
	{
		url : '/get_position_user.php',
		type : 'POST',
		data : 'id=' + id,
		dataType : 'html',
		success : function(code_html, statut)
		{
			if (code_html != "log" && code_html != "error")
				setMapProfile(code_html);
		}
	});
}
function setMapProfile(json)
{
	var arr = JSON.parse(json);
	latitude = arr[0];
	longitude = arr[1];
	if (arr[0] == "unknown" || arr[1] == "unknown" || arr[0] == "999" || arr[1] == "999")
	{
		getPositionProfile();
		return ;
	}
	got = 1;
	mapboxgl.accessToken = 'pk.eyJ1IjoibmJydWNrZXIiLCJhIjoiY2pkZGYwYW0zMDBxazJxbXNncHR6c2JuOSJ9.gCzIaNJ4TKf6ofuQp2sRcQ';
	map = new mapboxgl.Map(
	{
		container: 'map',
		style: 'mapbox://styles/mapbox/streets-v10',
		center: [longitude, latitude],
		zoom: 13
	});
	map.on('load', function ()
	{
		map.addLayer(
		{
			"id": "symbols_" + layer,
			"type": "symbol",
			"source":
			{
				"type": "geojson",
				"data":
				{
					"type": "FeatureCollection",
					"features": [
					{
						"type": "Feature",
						"properties": {},
						"geometry":
						{
							"type": "Point",
							"coordinates": [longitude, latitude]
						}
					}]
				}
			},
			"layout":
			{
				"icon-image": "triangle-stroked-15",
				"icon-size": 2
			}
		});
	});
}
function saveInfo(event)
{
	event.preventDefault();
	var data = new FormData();
	data.append('last_name', $("#last_name").val());
	data.append('first_name', $("#first_name").val());
	data.append('gender', $("#gender").val());
	data.append('men', $("#men").is(":checked"));
	data.append('women', $("#women").is(":checked"));
	data.append('bio', $("#bio").val());
	data.append('tags', JSON.stringify(tags));
	data.append('loc', $("input[name=loc]:checked").val());
	data.append('latitude', latitude);
	data.append('longitude', longitude);
	data.append('age', $("#age").val());
	if ($("#pic_0_after").attr('src') == "")
		data.append('pic_0', "removed");
	else
		data.append('pic_0', file_0);
	if ($("#pic_1_after").attr('src') == "")
		data.append('pic_1', "removed");
	else
		data.append('pic_1', file_1);
	if ($("#pic_2_after").attr('src') == "")
		data.append('pic_2', "removed");
	else
		data.append('pic_2', file_2);
	if ($("#pic_3_after").attr('src') == "")
		data.append('pic_3', "removed");
	else
		data.append('pic_3', file_3);
	if ($("#pic_4_after").attr('src') == "")
		data.append('pic_4', "removed");
	else
		data.append('pic_4', file_4);
	$.ajax(
	{
		url : '/modify_information.php',
		type : 'POST',
		data : data,
		processData: false,
		contentType: false
	});
}
function removePic(id)
{
	$("#pic_" + id + "_after").css("display", "none");
	$("#pic_" + id + "_trash").css("display", "none");
	$("#pic_" + id + "_before").css("display", "initial");
	$("#pic_" + id + "_after").attr('src', "");
	if (id == 0)
		$("#pic_" + id).css('margin-left', "-30px");
	else
		$("#pic_" + id).css('margin-left', "-15px");
}
$(document).on('dragenter', '#pic_0', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	$("#pic_0_before").attr('src', '/imgs/plus_good.svg');
});
$(document).on('dragover', '#pic_0', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	$("#pic_0_before").attr('src', '/imgs/plus_good.svg');
});
$(document).on('dragleave', '#pic_0', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	$("#pic_0_before").attr('src', '/imgs/plus.svg');
});
$(document).on('dragenter', '#pic_1', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	$("#pic_1_before").attr('src', '/imgs/plus_good.svg');
});
$(document).on('dragover', '#pic_1', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	$("#pic_1_before").attr('src', '/imgs/plus_good.svg');
});
$(document).on('dragleave', '#pic_1', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	$("#pic_1_before").attr('src', '/imgs/plus.svg');
});
$(document).on('dragenter', '#pic_2', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	$("#pic_2_before").attr('src', '/imgs/plus_good.svg');
});
$(document).on('dragover', '#pic_2', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	$("#pic_2_before").attr('src', '/imgs/plus_good.svg');
});
$(document).on('dragleave', '#pic_2', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	$("#pic_2_before").attr('src', '/imgs/plus.svg');
});
$(document).on('dragenter', '#pic_3', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	$("#pic_3_before").attr('src', '/imgs/plus_good.svg');
});
$(document).on('dragover', '#pic_3', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	$("#pic_3_before").attr('src', '/imgs/plus_good.svg');
});
$(document).on('dragleave', '#pic_3', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	$("#pic_3_before").attr('src', '/imgs/plus.svg');
});
$(document).on('dragenter', '#pic_4', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	$("#pic_4_before").attr('src', '/imgs/plus_good.svg');
});
$(document).on('dragover', '#pic_4', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	$("#pic_4_before").attr('src', '/imgs/plus_good.svg');
});
$(document).on('dragleave', '#pic_4', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	$("#pic_4_before").attr('src', '/imgs/plus.svg');
});
function srcOverPic(id)
{
	if ($("#pic_" + id + "_before").attr('src') == "/imgs/plus.svg")
		$("#pic_" + id + "_before").attr('src', '/imgs/plus_hover.svg');
}
function srcOutPic(id)
{
	if ($("#pic_" + id + "_before").attr('src') == "/imgs/plus_hover.svg")
		$("#pic_" + id + "_before").attr('src', '/imgs/plus.svg');
}
function get_pic(file, id)
{
	if (file == undefined)
		return ;
	if (id == 0)
		file_0 = file;
	else if (id == 1)
		file_1 = file;
	else if (id == 2)
		file_2 = file;
	else if (id == 3)
		file_3 = file;
	else if (id == 4)
		file_4 = file;
	if ((file.type == "image/png" || file.type == "image/jpeg") && file.size <= 1000000)
	{
		var reader = new FileReader();
		reader.onload = function(e)
		{
			$("#pic_" + id + "_after").attr('src', e.target.result);
			$("#pic_" + id + "_before").css("display", "none");
			$("#pic_" + id + "_after").css("display", "initial");
			$("#pic_" + id + "_trash").css("display", "initial");
			$("#pic_" + id).css('margin-left', "0px");
		}
		reader.readAsDataURL(file);
	}
	else
		$("#pic_" + id + "_before").attr('src', '/imgs/plus_error.svg');
}
function upload_pic(id)
{
	var file = document.getElementById("pic_" + id).files[0];
	get_pic(file, id);
}
$(document).on('drop', '#pic_0', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	var file = e.originalEvent.dataTransfer.files[0];
	get_pic(file, 0);
});
$(document).on('drop', '#pic_1', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	var file = e.originalEvent.dataTransfer.files[0];
	get_pic(file, 1);
});
$(document).on('drop', '#pic_2', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	var file = e.originalEvent.dataTransfer.files[0];
	get_pic(file, 2);
});
$(document).on('drop', '#pic_3', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	var file = e.originalEvent.dataTransfer.files[0];
	get_pic(file, 3);
});
$(document).on('drop', '#pic_4', function(e) 
{
	e.preventDefault();
	e.stopPropagation();
	var file = e.originalEvent.dataTransfer.files[0];
	get_pic(file, 4);
});
function getRealPosition()
{
	$.ajax(
	{
		url : '/get_real_position.php',
		dataType : 'html',
		success : function(code_html, statut)
		{
			if (code_html != "log" && code_html != "error")
				setAutoLoc(code_html);
		}
	});
}
function setAutoLoc(json)
{
	var arr = JSON.parse(json);
	if (arr[0] == "unknown" || arr[1] == "unknown" || arr[0] == "999" || arr[1] == "999")
	{
		getRealPosition();
		return ;
	}
	latitude = arr[0];
	longitude = arr[1];
	got = 1;
	map.removeLayer('symbols_' + layer);
	layer++;
	map.addLayer(
	{
		"id": "symbols_" + layer,
		"type": "symbol",
		"source":
		{
			"type": "geojson",
			"data":
			{
				"type": "FeatureCollection",
				"features": [
				{
					"type": "Feature",
					"properties": {},
					"geometry":
					{
						"type": "Point",
						"coordinates": [longitude, latitude]
					}
				}]
			}
		},
		"layout":
		{
			"icon-image": "triangle-stroked-15",
			"icon-size": 2
		}
	});
	map.flyTo({center: [longitude, latitude]});
}
function getPosition()
{
	$.ajax(
	{
		url : '/get_position.php',
		dataType : 'html',
		success : function(code_html, statut)
		{
			if (code_html != "log" && code_html != "error")
				setMap(code_html);
		}
	});
}
function setMap(json)
{
	var arr = JSON.parse(json);
	if (arr[0] == "unknown" || arr[1] == "unknown" || arr[0] == "999" || arr[1] == "999")
	{
		getPosition();
		return ;
	}
	latitude = arr[0];
	longitude = arr[1];
	got = 1;
	mapboxgl.accessToken = 'pk.eyJ1IjoibmJydWNrZXIiLCJhIjoiY2pkZGYwYW0zMDBxazJxbXNncHR6c2JuOSJ9.gCzIaNJ4TKf6ofuQp2sRcQ';
	map = new mapboxgl.Map(
	{
		container: 'map',
		style: 'mapbox://styles/mapbox/streets-v10',
		center: [longitude, latitude],
		zoom: 13
	});
	map.addControl(new MapboxGeocoder(
	{
		accessToken: mapboxgl.accessToken
	}));
	map.on('load', function ()
	{
		map.addLayer(
		{
			"id": "symbols_" + layer,
			"type": "symbol",
			"source":
			{
				"type": "geojson",
				"data":
				{
					"type": "FeatureCollection",
					"features": [
					{
						"type": "Feature",
						"properties": {},
						"geometry":
						{
							"type": "Point",
							"coordinates": [longitude, latitude]
						}
					}]
				}
			},
			"layout":
			{
				"icon-image": "triangle-stroked-15",
				"icon-size": 2
			}
		});
		map.on('click', function (e)
		{
			latitude = e.lngLat.lat;
			longitude = e.lngLat.lng;
			got = 1;
			map.removeLayer('symbols_' + layer);
			layer++;
			map.addLayer(
			{
				"id": "symbols_" + layer,
				"type": "symbol",
				"source":
				{
					"type": "geojson",
					"data":
					{
						"type": "FeatureCollection",
						"features": [
						{
							"type": "Feature",
							"properties": {},
							"geometry":
							{
								"type": "Point",
								"coordinates": [longitude, latitude]
							}
						}]
					}
				},
				"layout":
				{
					"icon-image": "triangle-stroked-15",
					"icon-size": 2
				}
			});
			$("#nauto_loc").prop('checked', true);
		});
	});
}
function loadTags()
{
	$.ajax(
	{
		url : '/get_tags.php',
		dataType : 'html',
		success : function(code_html, statut)
		{
			if (code_html != "log" && code_html != "error")
			{
				var arr = JSON.parse(code_html);
				for (var i = 0; i < arr.length; i++)
				{
					var div = '<div id=\"tag_' + index + '\" class=\"account_tags\">';
					div += '<img onclick=\"removeTag(\'tag_' + index + '\');\" class=\"account_tags\" src=\"/imgs/grey_remove.svg\" onmouseover=\"this.src=\'/imgs/red_remove.svg\'\" onmouseout=\"this.src=\'/imgs/grey_remove.svg\'\">';
					div += '<span id=\"text_tag_' + index + '\" class=\"account_tags\">' + arr[i] + '</span>';
					div += '</div>';
					$("#box_tags").append(div);
					tags.push(arr[i]);
					index++;
				}
			}
		}
	});
}
function removeTag(id)
{
	var text = $("#text_" + id).text();
	var text = tags.indexOf(text);
	if (text > -1)
		tags.splice(text, 1);
	$("#" + id).remove();
}
function changeInput(event)
{
	if (event.keyCode == 13)
	{
		var text = $('#tag').val();
		text = text.replace(/[^A-Za-z]/g, '');
		text = "#" + text;
		text = text.substring(0, 10);
		if (text.length <= 1 || tags.includes(text))
			return ;
		var div = '<div id=\"tag_' + index + '\" class=\"account_tags\">';
		div += '<img onclick=\"removeTag(\'tag_' + index + '\');\" class=\"account_tags\" src=\"/imgs/grey_remove.svg\" onmouseover=\"this.src=\'/imgs/red_remove.svg\'\" onmouseout=\"this.src=\'/imgs/grey_remove.svg\'\">';
		div += '<span id=\"text_tag_' + index + '\" class=\"account_tags\">' + text + '</span>';
		div += '</div>';
		$("#box_tags").append(div);
		$('#tag').val("#");
		tags.push(text);
		index++;
	}
	else
	{
		var text = $('#tag').val();
		text = text.replace(/[^A-Za-z]/g, '');
		text = "#" + text;
		text = text.substring(0, 10);
		$('#tag').val(text);
	}
}
