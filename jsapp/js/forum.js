(function() {

var sid = false;
var xmlrpcurl = '../api/';
var username = '';
var password = ''; // used for jabber login

var formatter;

var xmlrpc = new XMLRPC(xmlrpcurl, 'lima');
/* xmlrpc test routines

var xmlrpc = new XMLRPC('../xmlrpc/xmlrpc.php', 'lima');

xmlrpc.call('test', { 'arg1' : 'value1', 'arg2' : 'value2'  }, function(msg) { alert($(msg).text()); });
xmlrpc.multicall([
	{
		'proc' : 'test',
		'args' : {
			'arg1' : 'value1',
			'arg2' : 'value2'
		},
		'handler' : function(msg) {
			alert('call1 gives "' + $(msg).text() + '"');
		}
	}, {
		'proc' : 'test',
		'args' : {
			'arg1' : 'abc',
			'arg2' : 'def'
		},
		'handler' : function(msg) {
			alert('call2 gives "' + $(msg).text() + '"');
		}
	}
]);
*/

var login = function(user, pass) {
	user = user.trim();
	pass = pass.trim();
	if(user.length == 0 || pass.length == 0)
		return;
	$.ajax({
		url: xmlrpcurl,
		type: 'POST',
		dataType: 'xml',
		data: {
			'action' : 'login',
			'user' : user,
			'pass' : pass
		}
	}).done(function(msg) {
		var errorcode = $(msg).find('lima > errorcode').text();
		if(errorcode == 'passwd') {
			$('#passworderror').show();
			return;
		}
		$('#passworderror').hide();
		var loggedin = $(msg).find('lima > loggedin').text();
		if(loggedin == 'no') {
			sid = false;
			return;
		}
		sid = $(msg).find('lima > session').text();
		username = user;
		password = pass;
		saveSession();
		$('#loginbox').dialog('close');
		$('#main').show();
		loadContent();
		$('#password').val('');
	});
};

var loginfunction = function() {
	login($(this).find('#username').val(), $(this).find('#password').val());
};

var logout = function() {
	$.ajax({
		url: xmlrpcurl,
		type: 'POST',
		data: {
			'action' : 'logout',
			'sid' : sid
		}
	}).done(function(msg) {
		var loggedin = $(msg).find('lima > loggedin').text();
		if(loggedin != 'no') {
			alert('Es ist ein schwerer Fehler aufgetreten!');
			return;
		}
		sid = false;
		$('#main').hide();
		$('#loginbox').dialog('open');
		destroySession();
	});
};

var updater = function() {
	setInterval(function() {
		if(!sid)
			return;
		doUpdate();
	}, 60000 * 2); // 2 min
};

var doUpdate = function() {
	loadHomescreen(true);
	loadMessages(true);
	loadStatus(true);
};

var format = function(xml) {
	var result = [];
	xml.children().each(function() {
		switch(this.nodeName) {
			case 'text':
				result.push(document.createTextNode($(this).text()));
				break;
			case '#text':
				break;
			case 'img':
				var url = $(this).attr('src');
				var alt = $(this).attr('alt');
				var node = document.createElement('img');
				$(node).attr('src', url);
				$(node).attr('alt', alt);
				$(node).append(format($(this)));
				result.push(node);
				break;
			case 'goto':
				var type = $(this).attr('type');
				var node = document.createElement('a');
				$(node).data('type', type);
				$(node).attr('href', '#');
				$(node).attr('onclick', 'return false');
				switch(type) {
					case 'board':
						var id = $(this).attr('id');
						$(node).data('id', id);
						break;
					case 'thread':
						var url = $(this).attr('url');
						$(node).data('url', url);
						$(node).click(function() {
							showThread($(this).data('url'));
						});
						break;
				}
				$(node).append(format($(this)));
				result.push(node);
				break;
			case 'link':
				var url = $(this).attr('url');
				var node = document.createElement('a');
				$(node).attr('href', url);
				$(node).append(format($(this)));
				result.push(node);
				break;
			case 'code':
				var pre = document.createElement('pre');
				var language = $(this).attr('language');
				var display = $(this).attr('display');
				if(language != undefined) {
					$(pre).append(format($(this)));
					$(pre).addClass('brush: ' + language);
				} else {
					var code = document.createElement('code');
					$(code).append(format($(this)));
					$(pre).append(code);
				}
				if(display == 'inline')
					$(pre).css('display', 'inline');
				result.push(pre);
				break;
			case 'youtube':
				var url = $(this).text().trim();
				var node = document.createElement('iframe');
				$(node).attr('src', 'https://www.youtube.com/embed/' + url + '?rel=0');
				$(node).attr('allowfullscreen', '');
				$(node).attr('frameborder', '0');
				$(node).attr('height', '257');
				$(node).attr('width', '400');
				result.push(node);
				break;
			default:
				var node = document.createElement(this.nodeName);
				$(node).append(format($(this)));
				result.push(node);
				break;
		}
	});
	return $(result);
};

var threadWriteEnable = function() {
	$('#thread-write-enable').hide();
	$('#thread-write').show();
};

var threadWriteDisable = function() {
	$('#thread-write').hide();
	$('#thread-write-enable').show();
};

var threadPost = function() {
	var text = $('#thread-write-content').val().trim();
	if(text.length() < 15) {
		// TODO: show warning
		return;
	}
};

var showThread = function(url) {
	$('#tabs').tabs('select', '#tab-thread');
	$('#thread-write').hide();
	$('#thread-write-enable').hide();
	$('#thread-title').text('Lade...');
	$('#thread-content').empty();
	$.ajax({
		url: xmlrpcurl,
		type: 'POST',
		data: {
			action : 'thread',
			name : url,
			sid : sid
		}
	}).done(function(msg) {
		var title = $(msg).find('lima > thread > name').text();
		var writable = $(msg).find('lima > thread > writable').text();
		// reset input text area
		$('#thread-write-content').val('');
		$('#thread-title').text(title);
		$('#thread-write').hide();
		writable ? $('#thread-write-enable').show() : $('#thread-write-enable').hide();
		$(msg).find('lima > thread > posts > post').each(function(index) {
			var type = $(this).find('type').text();
			var date = $(this).find('date').text();
			var id = $(this).find('id').text();
			var userdata = $(this).find('user');
			var user = userdata.text();
			var userdeleted = userdata.attr('deleted');
			var avatar = userdata.attr('avatar');
			var gulden = userdata.attr('gulden');
			var rank = userdata.attr('rank');
			var role = userdata.attr('role');
			var starcount = userdata.attr('starcount');
			var content = format($(this).find('content'));

			var node = $('<li>');
			var userinfo = document.createTextNode(user);
			if(userdeleted == 'true')
				userinfo = $('<del>').text(user);
			var data = [];
			data.push($('<p class="username">').append(userinfo));
			data.push($('<p class="date">').text(date));
			data.push($('<p class="limalink"><a href="https://www.lima-city.de/board/action%3Ajump/' + id + '" target="_blank">@lima-city</a></p>'));
			if(userdeleted == 'false') {
				if(avatar != '')
					data.push($('<p class="avatar">').append($('<img>').attr('src', 'https://www.lima-city.de/images/avatar/' + avatar)));
				data.push($('<p class="rank">').text(rank + ', ' + gulden + ' Gulden'));
				var starurl = 'images/stars/star_green.png';
				if(role == 'Moderator')
					starurl = 'images/star_m.png';
				if(role == 'Co-Admin')
					starurl = 'images/star_k.png';
				var stars = $('<p class="stars">');
				for(var i = 0; i < starcount; i++)
					stars.append($('<img>').attr('src', starurl));
				data.push(stars);
			}
			node.append($('<div class="info">').append(data));
			node.append($('<div class="content">').append(content));
			node.addClass('ui-widget').addClass('ui-widget-content').addClass('ui-corner-all');
			if(type == 'deleted')
				node.addClass('deleted');
			// ui-menu ui-widget ui-widget-content ui-corner-all
			$('#thread-content').append(node);
		});
		SyntaxHighlighter.highlight();
	});
};

var loadHomescreen = function(update) {
	if(!update) {
		$('#newest').empty();
		$('#newest').append($('<li>Lade...</li>'));
		$('#newest').menu('refresh');
	}
	$.ajax({
		url: xmlrpcurl,
		type: 'POST',
		data: {
			'action' : 'homepage',
			'sid': sid
		}
	}).done(function(msg) {
		$('#newest').empty();
		$(msg).find('lima > homepage > newest > thread').each(function(index) {
			var important = $(this).find('flags').attr('important');
			var fixed = $(this).find('flags').attr('fixed');
			var closed = $(this).find('flags').attr('closed');
			var name = $(this).find('name').text();
			var url = $(this).find('url').text();
			var date = $(this).find('date').text();
			var forum = $(this).find('forum').text();
			var forumurl = $(this).find('forum').attr('url');
			var user = $(this).find('user').text();

			var info = '';
			var icons = [];
			if(important == 'true' || fixed == 'true' || closed == 'true') {
				info = '<br />Flags: ';
				var x = false;
				if(important == 'true') {
					info += 'wichtig';
					x = true;
					icons.push($('<img src="images/icons/flag_green.png" alt="Wichtig"/>'));
				}
				if(fixed == 'true') {
					if(x) {
						info += ', ';
						icons.push(document.createTextNode(' '));
					}
					x = true;
					info += 'fixiert';
					icons.push($('<img src="images/icons/lightning.png" alt="Fixiert"/>'));
				}
				if(closed == 'true') {
					if(x) {
						info += ', ';
						icons.push(document.createTextNode(' '));
					}
					info += 'geschlossen';
					icons.push($('<img src="images/icons/lock.png" alt="Geschlossen"/>'));
				}
			}
			var tooltip = 'Forum: ' + forum + '<br />' +
				'Datum: ' + date + '<br />' +
				'Post-Author: ' + user +
				info;

			var node = $('<a href="#" onclick="return false">');
			node.click(function() {
				showThread($(this).data('url'));
			});
			node.data('url', url);
			node.data('forumurl', forumurl);
			node.append(document.createTextNode(name));
			if(icons.length != 0) {
				node.append(document.createTextNode(' '));
				node.append(icons);
			}
			$('#newest').append($('<li title="thread">').append(node).tooltip({ content : tooltip }));
		});
		$('#newest').menu('refresh');
	});
};

var loadBoards = function(update) {
	if(!update) {
		$('#boards').empty();
		$('#boards').append($('<li>Lade...</li>'));
		$('#boards').menu('refresh');
	}
	$.ajax({
		url: xmlrpcurl,
		type: 'POST',
		data: {
			'action' : 'forumlist',
			'sid': sid
		}
	}).done(function(msg) {
		$('#boards').empty();
		$(msg).find('lima > forum > board').each(function(index) {
			var name = $(this).find('name').text();
			var url = $(this).find('url').text();
			var description = $(this).find('description').text();
			var topics = $(this).find('topics').text();
			var replies = $(this).find('answers').text();
			var newest_title = $(this).find('newestThread').find('title').text();
			var newest_author = $(this).find('newestThread').find('author').text();
			var newest_date = $(this).find('newestThread').find('date').text();
			var newest_url = $(this).find('newestThread').find('url').text();

			var tooltip = description + '<br />' +
				'Themen: ' + topics + '<br />' +
				'Antworten: ' + replies + '<br />' +
				'Neuester Thread: ' + newest_title;

			var node = $('<a href="#" onclick="return false">').text(name);
			$('#boards').append($('<li title="board">').append(node).tooltip({ content : tooltip }));
		});
		$('#boards').menu('refresh');
	});
};

var loadMessages = function(update) {
	if(!update) {
		$('#messages').empty();
		$('#messages').append($('<li>Lade...</li>'));
		$('#messages').menu('refresh');
	}
	$.ajax({
		url: xmlrpcurl,
		type: 'POST',
		data: {
			'action' : 'messages',
			'sid': sid
		}
	}).done(function(msg) {
		$('#messages').empty();
		$(msg).find('lima > messages > message').each(function(index) {
			var id = $(this).find('id').text();
			var title = $(this).find('title').text();
			var date = $(this).find('date').text();
			var from = $(this).find('from').text();

			var tooltip = 'Von: ' + from + '<br />' +
				'Datum: ' + date + '<br />' +
				'Nachrichten-ID: ' + id;

			var node = $('<a href="#" onclick="return false">').text(title);
			$('#messages').append($('<li title="message">').append(node).tooltip({ content : tooltip }));
		});
		$('#messages').menu('refresh');
	});
};

var loadStatus = function(update) {
	if(!update) {
		$('#status').empty();
		$('#status').append($('<div>Lade...</div>'));
	}
	$.ajax({
		url: xmlrpcurl,
		type: 'POST',
		data: {
			action : 'serverstatus',
			sid : sid
		}
	}).done(function(msg) {
		$('#status').empty();
		$(msg).find('lima > serverstatus > info').each(function(index) {
			var name = $(this).attr('name');
			var time = $(this).attr('time');
			var online = time != '';

			var node = $('<div>').text(name + ': ' + (online ? time : 'Ausgefallen'));
			if(!online)
				node.css('color', 'red');
			$('#status').append(node);
		});
	});
};

var loadContent = function() {
	loadHomescreen();
	loadBoards();
	loadMessages();
	loadStatus();
};

var loadSession = function() {
	if(!sessionStorage.sid)
		return false;
	if(sessionStorage.sid == 'undefined')
		return false;
	username = sessionStorage.username;
	password = sessionStorage.password;
	sid = sessionStorage.sid;
	return true;
};

var saveSession = function() {
	localStorage.username = username;
	sessionStorage.sid = sid;
};

var destroySession = function() {
	sessionStorage.sid = 'undefined';
};

var init = function() {
	// initialize bbcode formatter
	formatter = new BBFormatter(limarules);
	formatter.setSmilies(limasmilies);

	// initialize login box
	$('#loginbox').dialog({
		autoOpen: false,
		closeOnEscape: false,
		modal: false,
		resizable: false,
		buttons: {
			Login: loginfunction
		},
		beforeClose: function(event, ui) {
			return sid != false;
		}
	});
	$('#loginbox form').submit(loginfunction);

	// initialize logout button
	$('#logout').button();
	$('#logout').click(logout);

	// initialize webspace button
	$('#webspace').button();
	$('#webspace').click(function() {
		window.open('http://' + username + '.lima-city.de/');
	});

	// initialize update button
	$('#update').button();
	$('#update').click(doUpdate);

	// initialize thread write enable button
	$('#thread-write-enable').button();
	$('#thread-write-enable').click(threadWriteEnable);

	// initialize cancel button (thread)
	$('#thread-write button.cancel').button();
	$('#thread-write button.cancel').click(threadWriteDisable);

	// initialize save button (thread)
	$('#thread-write button.save').button();
	$('#thread-write button.save').click(threadPost);

	// initialize formatting function
	$('#thread-write-content').keyup(function() {
		var input = $(this).val();
		var html = formatter.format(input);
		$('#thread-preview-text').html(html);
		$('#thread-preview-chars').html('<i>Zeichen: <b>' + html2plain(html).length + '</b></i>');
		SyntaxHighlighter.highlight();
	});
	$('#thread-preview-chars').html('<i>Zeichen: <b>0</b></i>');

	// initialize newest threads list
	$('#newest').menu();

	// initialize board list
	$('#boards').menu();

	// initialize message list
	$('#messages').menu();

	// initialize tabs
	$('#tabs').tabs();

	$('#thread-title').html('<i>kein Thread ge&ouml;ffnet</i>');

	// hide elements
	$('#passworderror').hide();
	$('#main').hide();
	$('#thread-write').hide();
	$('#thread-write-enable').hide();

	updater();

	if(loadSession()) {
		$('#main').show();
		loadContent();
	} else {
		if((localStorage.username != 'undefined') && localStorage.username)
			$('#username').val(localStorage.username);
		$('#loginbox').dialog('open');
	}
};

$(init);

})();