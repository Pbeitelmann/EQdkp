//Element to resize
scale_object = null;
corner = null;
member_id = null;
time_id = null;

//Mouse Position
posx = 0;
startx = 0;
clickx1 = 0;
clickx2 = 0;
//Object data
oldx = 0;
max_right = 0;
min_left = 0;

//max positions
posi_null = 0;
posi_max = 0;

//input fields
joiner = null;
leaver = null;

korrektur = 0;

document.onmousemove = scale;
document.onmousedown = set_clickx;
document.onmouseup = stop_scale;

function set_member(member_key, px_time) {
	if(!posi_null) {
		posi_null = $('#member_' + member_key).offset().left;		
		posi_null = parseInt(posi_null);
		console.log(posi_null);
		padding_left = $('#member_' + member_key).css('paddingLeft');
		padding_left = padding_left.replace("px", "");
		padding_left = parseInt(padding_left);
		posi_null = posi_null + padding_left;
	}
	
	if(!posi_max) {
		posi_max = posi_null + parseInt(px_time);
	}
	console.log('posi_max '+posi_max+' posi_null '+posi_null);
	member_id = parseInt(member_key);
}

function set_time_key(time_key) {
	time_id = parseInt(time_key.substr(-1));
}

function scale_start(type) {
	var element_id = "times_" + member_id + "_" + time_id;
	
	jq_scale_obj = $('#'+element_id);
	jq_scale_obj_offset = jq_scale_obj.offset();
	jq_scale_obj_offset = parseInt(jq_scale_obj_offset.left);
	
	oldx = posx - jq_scale_obj_offset;

	scale_object = document.getElementById(element_id);
	joiner = document.getElementById(element_id + "j");
	leaver = document.getElementById(element_id + "l");
	//oldx = posx - scale_object.offsetLeft;
	
	console.log('scale start() posx '+posx+ ' offset '+ jq_scale_obj_offset);
	startx = posx;
	corner = type;
    var after = document.getElementById("times_" + member_id + "_" + (time_id+1));
    max_right = posi_max;
    var previous = document.getElementById("times_" + member_id + "_" + (time_id-1));
    min_left = 0;
    if(after != null) {
    	max_right = posi_null + parseInt(after.style.marginLeft) - 1;
    }
    if(previous != null) {
    	min_left = parseInt(previous.style.marginLeft) + parseInt(previous.style.width) + 1;
    }
}

function stop_scale() {
	console.log('stop_scale()');
	
	scale_object = null;
	joiner = null;
	leaver = null;
	startx = 0;
	oldx = 0;
	max_right = 0;
	left_edge = 0;
	right_edge = 0;
	corner = null;
}

function scale(ereignis) {
	posx = document.all ? window.event.clientX : ereignis.pageX;
	if(scale_object != null) {
		console.log('scale() corner '+corner);
		console.log('posx '+posx+' oldx '+oldx+' posi_null '+posi_null);
		
		if(corner == "left") {
			set_left((posx - oldx - posi_null), 1);
        	if(scale_object != null) set_width((parseInt(scale_object.style.width) + (startx - posx)), true);
		}
		if(corner == "right") {
			set_width(parseInt(scale_object.style.width) + (posx - startx));
		}
		if(corner == "middle") {
			set_left((posx - oldx - posi_null + korrektur));
		}
        startx = posx;
	}
}

function set_clickx(ereignis) {
	//record last two clicks
	clickx1 = clickx2;
	clickx2 = document.all ? window.event.clientX : ereignis.pageX;
	console.log('click x2 '+clickx2);
}

function set_width(w, nostopper) {
	console.log('set_width w'+w);
	var stopper = false;
	if(w > 0) {
		if(w+posi_null+parseInt(scale_object.style.marginLeft) > max_right) {
			w = max_right - parseInt(scale_object.style.marginLeft) - posi_null;
			stopper = true;
		}
        scale_object.style.width = w + "px";
		leaver.value = $('#member_form').data('raid_start') + (parseInt(scale_object.style.marginLeft) + w)*20;
	} else {
        scale_object.style.width = 1 + "px";
		leaver.value = $('#member_form').data('raid_start') + (parseInt(scale_object.style.marginLeft) + w)*20;
		stopper = true;
	}
	if(stopper && !nostopper) {
		stop_scale();
	}
}

function set_left(l, nostopper) {
	console.log('set_left l'+l);
	
	var stopper = false;
	if(l >= min_left) {
		if(l > max_right - posi_null - parseInt(scale_object.style.width)) {
			l = max_right - posi_null - parseInt(scale_object.style.width);
			stopper = 2;
		}
		scale_object.style.marginLeft = l + "px";
	} else {
		scale_object.style.marginLeft = min_left + "px";
		stopper = 1;
	}
    joiner.value = $('#member_form').data('raid_start') + l*20;
    leaver.value = $('#member_form').data('raid_start') + (l + parseInt(scale_object.style.width))*20;
	if(stopper && (!nostopper || nostopper == stopper)) {
		stop_scale();
	}
}

function add_timeframe() {
	posx = clickx1;
	//between which times did the user click?
	var all_times = $('#times_' + member_id + ' > div');
	var left = new Array(0, posi_null);
	var right = new Array(0, posi_max);
	for(var i=0; i < all_times.length; i++) {
		var current = new Array();
		current.offset = $(all_times[i]).offset();
		current.right_edge = (current.offset.left + $(all_times[i]).width());
		if(current.right_edge < posx && current.right_edge > left[1]) {
			left[0] = $(all_times[i]).attr('id');
			left[1] = current.right_edge;
		}
		if(current.offset.left > posx && current.offset.left < right[1]) {
			right[0] = $(all_times[i]).attr('id');
			right[1] = current.offset.left;
		}
	}
	var change_id = '';
	var lgth = 'times_' + member_id + '_';
	var object_to_add = $('#times_' + member_id + '_99').clone();
	var selector = '';
	var type = 'after';
	var new_time_key = 0;
	//is there an element left of mouse?
	if(left[0]) {
		new_time_key = parseInt(left[0].substr(lgth.length)) + 1;
		object_to_add.attr('id', 'times_' + member_id + '_' + new_time_key);
		change_id = $('#' + left[0] + ' ~ div');
		selector = '#' + left[0];
	} else { //its not so change all elements
		object_to_add.attr('id', 'times_' + member_id + '_0');
		change_id = $('#times_' + member_id + '_0, #times_' + member_id + '_0 ~ div');
		selector = '#times_' + member_id + '_1';
		type = 'before';
	}
	//no time there
	if(!right[0] && !left[0]) {
		selector = '#times_' + member_id;
		type = 'prepend';
	}
	for(i=(change_id.length -1); i>=0; i--) {
		if(!isNaN(parseInt(change_id[i].id.substr(lgth.length)))) {
			change_id_of_input(change_id[i].id, (parseInt(change_id[i].id.substr(lgth.length)) + 1));
			change_id[i].id = 'times_' + member_id + '_' + (parseInt(change_id[i].id.substr(lgth.length)) + 1);
		}
	}
	if(left[1]) {
		left[1] = left[1] - posi_null;
	}
	object_to_add.css('margin-left', (left[1] + 2) + 'px');
	if(!right[1]) {
		right[1] = 12;
	}
	object_to_add.css('width', (right[1] - left[1] - posi_null - 4) + 'px');
	if(type == 'before') {
		$(selector).before(object_to_add);
	} else if(type == 'after') {
		$(selector).after(object_to_add);
	} else if(type == 'prepend') {
		$(selector).prepend(object_to_add);
	}
	change_id_of_input('times_' + member_id + '_99', new_time_key);  

	$('#times_' + member_id + '_' + new_time_key + 'j').val($('#member_form').data('raid_start') + (left[1]+2)*20);
	$('#times_' + member_id + '_' + new_time_key + 'j').val($('#member_form').data('raid_start') + (left[1]+2)*20);
	$('#times_' + member_id + '_' + new_time_key + 'l').val($('#member_form').data('raid_start') + (left[1] + right[1])*20);
	$('#times_' + member_id + '_' + new_time_key + 'l').removeAttr('disabled');
	$('#times_' + member_id + '_' + new_time_key + 'j').removeAttr('disabled');
	$('#times_' + member_id + '_' + new_time_key + 's').removeAttr('disabled');
}

function remove_timeframe() {
	if (member_id != null && time_id != null){	
		var change_id = $('#times_' + member_id + '_' + time_id + ' ~ div');
		$('#times_' + member_id + '_' + time_id).remove();
		var lgth = 'times_' + member_id + '_';
		for(var i=0; i < change_id.length; i++) {
			if(!isNaN(parseInt(change_id[i].id.substr(lgth.length)))) {
				change_id_of_input(change_id[i].id, (parseInt(change_id[i].id.substr(lgth.length)) -1));
				change_id[i].id = "times_" + member_id + "_" + (parseInt(change_id[i].id.substr(lgth.length)) -1);
			}
		}
	}
}

function change_standby() {
	var input_id = 'times_' + member_id + '_' + time_id;
	if($('#' + input_id + 's').val() == 'standby') {
		$('#' + input_id + 's').attr('value', '0');
		$('#' + input_id).attr('class', 'time');
	} else {
		$('#' + input_id + 's').attr('value', 'standby');
		$('#' + input_id).attr('class', 'timestandby');
	}
}

function change_id_of_input(oldid, newid) {
    $('#' + oldid + "j").attr('name', 'members[' + member_id + '][times][' + newid + '][join]');
    $('#' + oldid + "j").attr('id', "times_" + member_id + "_" + newid + "j");
    $('#' + oldid + "l").attr('name', 'members[' + member_id + '][times][' + newid + '][leave]');
    $('#' + oldid + "l").attr('id', "times_" + member_id + "_" + newid + "l");
    if($('#' + oldid + 's')) {
    	$('#' + oldid + "s").attr('name', 'members[' + member_id + '][times][' + newid + '][extra]');
        $('#' + oldid + "s").attr('id', "times_" + member_id + "_" + newid + "s");
    }
}