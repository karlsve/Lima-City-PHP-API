(function() {

var sid = false;
var xmlrpcurl = '../api/';
var username = '';
var password = ''; // used for jabber login
var currentmailbox = 1;
var userlistfilter = 'online';

var formatter;

var xmlrpc = new XMLRPC('../rpc/xmlrpc.php', 'lima');

var getCurrentMailbox = function() {
	return currentmailbox;
};

var setCurrentMailbox = function(mbox) {
	currentmailbox = mbox;
};

var getUsername = function() {
	return username;
};

var setUsername = function(user) {
	username = user;
};

var setPassword = function(pass) {
	password = pass;
};

var login = function(user, pass) {
	user = user.trim();
	pass = pass.trim();
	if(user.length == 0 || pass.length == 0)
		return;
	xmlrpc.call('login', { 'username' : user, 'password' : pass }, function(msg) {
		var errorcode = $(msg).find('errorcode').text();
		if(errorcode == 'passwd') {
			$('#passworderror').show();
			return;
		}
		$('#passworderror').hide();
		var error = $(msg).find('result').text();
		if(error != 'OK') {
			sid = false;
			return;
		}
		sid = $(msg).find('session').text();
		setUsername(user);
		setPassword(pass);
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
	xmlrpc.call('logout', { 'sid' : sid }, function(msg) {
		var error = $(msg).find('fail').length;
		if(error != 0) {
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
	setInterval(function() {
		$('#currenttime').text(getDate());
	}, 2000);
};

var doUpdate = function() {
	loadHomescreen(true);
	loadMessages(true);
	loadStatus(true);
	loadNotifications();
	loadUserlist(true);
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
			case 'math':
				var url = $(this).find('url').text();
				var alt = $(this).find('raw').text();
				var node = document.createElement('img');
				$(node).attr('src', 'http://latex.codecogs.com/png.latex?\\color{white}{' + url + '}');
				$(node).attr('alt', alt);
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

var threadWrite = function(quote) {
	// TODO: load quote data
};

var reportSpam = function(id) {
	$('#reportspam textarea.comment').val('');
	$('#reportspam').data('postid', id);
	$('#reportspam').dialog('open');
};

var reportspamfunction = function() {
	var id = $('#reportspam').data('postid');
	var comment = $('#reportspam textarea.comment').val();
	alert('Spamreport for ' + id + ':\n' + comment);
	$('#reportspam').dialog('close');
};

var showThread = function(url) {
	$('#tabs').tabs('select', '#tab-thread');
	$('#thread-write').hide();
	$('#thread-write-enable').hide();
	$('#thread-title').text('Lade...');
	$('#thread-content').empty();
	xmlrpc.call('getThread', { 'sid' : sid, 'url' : url }, function(msg) {
		var notloggedin = $(msg).find('notloggedin').length != 0;
		if(notloggedin)
			return;
		var title = $(msg).find('name').text();
		var writable = $(msg).find('writable').text();
		// reset input text area
		$('#thread-write-content').val('');
		$('#thread-title').text(title);
		$('#thread-write').hide();
		writable ? $('#thread-write-enable').show() : $('#thread-write-enable').hide();
		$(msg).find('posts > post').each(function(index) {
			var type = $(this).find('type').text();
			var date = $(this).find('date').text();
			var id = $(this).find('id').text();
			var userdata = $(this).find('user');
			var user = userdata.text();
			var userdeleted = userdata.attr('deleted');
			var author = userdata.attr('author');
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
			if(author == 'true')
				data.push($('<p class="author">Author dieses Themas</p>'));
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

			var actions = $('<ul class="actions">');
			actions.append($('<li><img src="images/icons/bug.png" /></li>').click(function() {
				reportSpam(id);
			}));
			if(writable == 'true')
				actions.append($('<li><img src="images/icons/comments.png" /></li>').click(function() {
					threadWrite(id);
				}));

			node.append($('<div class="info">').append(data));
			node.append($('<div class="content">').append(actions).append(content));
			node.addClass('ui-widget').addClass('ui-widget-content').addClass('ui-corner-all');
			if(type == 'deleted')
				node.addClass('deleted');
			// ui-menu ui-widget ui-widget-content ui-corner-all
			$('#thread-content').append(node);
		});
		SyntaxHighlighter.highlight();
	});
};

var showMessage = function(id) {
	$('#tabs').tabs('select', '#tab-messages');
	xmlrpc.call('getMessage', { 'sid' : sid, 'id' : id }, function(msg) {
		var title = $(msg).find('title').text();
		var from = $(msg).find('from').text();
		var to = $(msg).find('to').text();
		var date = $(msg).find('date').text();
		$('#messagereader-title').text(title);
		$('#messagereader-from').text(from);
		$('#messagereader-to').text(to);
		$('#messagereader-date').text(date);
		$('#messagereader-content').empty();
		$('#messagereader-content').append(format($(msg).find('content')));

		$('#messages-listing').hide();
		$('#messagereader').show();
		loadMessages(true);
		loadNotifications();
	});
};

var showMessageList = function() {
	$('#messages-listing').show();
	$('#messagereader').hide();
};

var loadHomescreen = function(update) {
	if(!update) {
		$('#newest').empty();
		$('#newest').append($('<li>Lade...</li>'));
		$('#newest').menu('refresh');
	}
	xmlrpc.call('getHomepage', { 'sid' : sid }, function(msg) {
		var notloggedin = $(msg).find('notloggedin').length != 0;
		if(notloggedin)
			return;
		$('#newest').empty();
		$(msg).find('newest > thread').each(function(index) {
			var important = $(this).find('flags important').text();
			var fixed = $(this).find('flags fixed').text();
			var closed = $(this).find('flags closed').text();
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

			var node = $('<a>');
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
			if(user == username)
				node.addClass('ownpost');
			else if(forum == 'Spam-Forum')
				node.addClass('spamforum');
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
	xmlrpc.call('getBoards', { 'sid' : sid }, function(msg) {
		var notloggedin = $(msg).find('notloggedin').length != 0;
		if(notloggedin)
			return;
		$('#boards').empty();
		$(msg).find('board').each(function(index) {
			var name = $(this).find('name').text();
			var url = $(this).find('url').text();
			var description = $(this).find('description').text();
			var topics = $(this).find('topics').text();
			var replies = $(this).find('replies').text();
			var newest_thread = $(this).find('newest-thread');
			var newest_title = newest_thread.find('title').text();
			var newest_author = newest_thread.find('author').text();
			var newest_date = newest_thread.find('date').text();
			var newest_url = newest_thread.find('url').text();

			var tooltip = description + '<br />' +
				'Themen: ' + topics + '<br />' +
				'Antworten: ' + replies + '<br />' +
				'Neuester Thread: ' + newest_title;

			var node = $('<a>').text(name);
			$('#boards').append($('<li title="board">').append(node).tooltip({ content : tooltip }));
		});
		$('#boards').menu('refresh');
	});
};

var loadMessages = function(update, mailboxid) {
	if(!update) {
		if(mailboxid == undefined) {
			$('#mailboxes').empty();
			$('#mailboxes').append($('<li>Lade...</li>'));
			$('#mailboxes').menu('refresh');
		}
		$('#messages').empty();
		$('#messages').append($('<li>Lade...</li>'));
		$('#messages').menu('refresh');
	}
	var currentMailbox = getCurrentMailbox();
	var mbox = (mailboxid != undefined) ? mailboxid : currentMailbox;
	xmlrpc.multicall([
		{
			proc : 'getMessages',
			args : { 'sid' : sid, 'mailbox' : mbox },
			handler : function(msg) {
				var notloggedin = $(msg).find('notloggedin').length != 0;
				if(notloggedin)
					return;
				setCurrentMailbox(mbox);
				$('#messages').empty();
				$(msg).find('message').each(function(index) {
					var id = $(this).find('id').text();
					var title = $(this).find('title').text();
					var date = $(this).find('date').text();
					var from = $(this).find('from').text();
					var unread = $(this).find('unread').text();

					var tooltip = 'Von: ' + from + '<br />' +
						'Datum: ' + date + '<br />' +
						'Nachrichten-ID: ' + id;
					if(unread == 'true')
						tooltip += '<br />Ungelesen';

					var node = $('<a>').text(title);
					node.click(function() {
						showMessage(id);
					});
					var li = $('<li title="message">').append(node).tooltip({ content : tooltip });
					if(unread == 'true')
						li.addClass('message-unread');
					$('#messages').append(li);
				});
				$('#messages').menu('refresh');
			}
		}, {
			proc : 'getMailboxes',
			args : { 'sid' : sid },
			handler : function(msg) {
				var notloggedin = $(msg).find('notloggedin').length != 0;
				if(notloggedin)
					return;
				$('#mailboxes').empty();
				$(msg).find('mailbox').each(function(index) {
					var title = $(this).find('title').text();
					var id = $(this).find('id').text();

					var node = $('<a>').text(title);
					node.click(function() {
						loadMessages(false, id);
					});
					var li = $('<li>').append(node);
					if(id == mbox)
						li.addClass('ui-state-active').addClass('ui-corner-all');
					$('#mailboxes').append(li);
				});
				$('#mailboxes').menu('refresh');
				if(!update)
					showMessageList();
			}
		}
	]);
};

var loadNotifications = function() {
	xmlrpc.call('getNotifications', { 'sid' : sid }, function(msg) {
		var notloggedin = $(msg).find('notloggedin').length != 0;
		if(notloggedin)
			return;
		var notifications = [];
		var notificationcount = 0;
		var names = {
			'spam' : 'Spam',
			'guestbook' : 'Gästebucheinträge',
			'messages' : 'Nachrichten',
			'promowall' : 'Banner',
			'notification' : 'Benachrichtigungen'
		};
		var notificationtype = 'none';
		$(msg).find('notification').each(function(index) {
			var type = $(this).find('type').text();
			var count = $(this).find('count').text();
			notifications.push(names[type] + ': ' + count);
			notificationcount += parseInt(count);
			notificationtype = type;
		});
		var text = 'keine Benachrichtigungen';
		if(notifications.length == 1)
			text = notifications.pop();
		else if(notifications.length > 1) {
			text = notificationcount + ' Benachrichtigungen';
			notificationtype = 'many';
		}
		$('#notifications').data('type', notificationtype);
		$('#notifications').text(text);
	});
};

var loadUserlist = function(update) {
	if(!update) {
		$('#userlist').empty();
		$('#userlist').append($('<div>Lade...</div>'));
	}
	xmlrpc.call('getUsers', { 'sid' : sid, 'filter' : userlistfilter }, function(msg) {
		$('#userlist').empty();
		$(msg).find('user').each(function(index) {
			var name = $(this).find('name').text();
			var gulden = $(this).find('gulden').text();
			var rank = $(this).find('rang').text();
			var lastlogin = $(this).find('last-login').text();

			var tooltip = 'Benutzername: ' + name + '<br />'
				+ 'Gulden: ' + gulden + '<br />'
				+ 'Rang: ' + rank + '<br />'
				+ 'Letzter Login: ' + lastlogin;

			var node = $('<a>').text(name);
			if(name == username)
				node.addClass('self');
			$('#userlist').append($('<li title="user">').append(node).tooltip({ content : tooltip }));
		});
		$('#userlist').menu('refresh');
	});
};

var loadStatus = function(update) {
	if(!update) {
		$('#status').empty();
		$('#status').append($('<div>Lade...</div>'));
	}
	xmlrpc.call('getServerStatus', null, function(msg) {
		$('#status').empty();
		var servererrors = [];
		$(msg).find('info').each(function(index) {
			var name = $(this).attr('name');
			var time = $(this).attr('time');
			var online = time != '';

			var node = $('<div>').text(name + ': ' + (online ? time : 'Ausgefallen'));
			if(!online) {
				node.css('color', 'red');
				servererrors.push(name);
			}
			$('#status').append(node);
		});
		if(servererrors.length != 0) {
			if(servererrors.length == 1) {
				var name = servererrors.pop();
				$('#serverstatus').text('Ausfall: ' + name).css('color', 'red');
			} else
				$('#serverstatus').text('mehrere Server down').css('color', 'red');
		} else
			$('#serverstatus').text('Server: OK').css('color', 'green');
	});
};

var loadContent = function() {
	loadHomescreen();
	loadBoards();
	loadMessages();
	loadStatus();
	loadNotifications();
	loadUserlist();
	$('#currenttime').text(getDate());
};

var loadSession = function() {
	if(!sessionStorage.sid)
		return false;
	if(sessionStorage.sid == 'undefined')
		return false;
	username = localStorage.username;
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

	// initialize spam report button
	$('#reportspam').dialog({
		autoOpen: false,
		closeOnEscape: true,
		modal: true,
		resizable: false,
		buttons: {
			OK : reportspamfunction,
			Abbrechen : function() {
				$('#reportspam').dialog('close');
			}
		}
	});
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

	// initialize back button on message reader
	$('#messagereader button.back').button();
	$('#messagereader button.back').click(showMessageList);

	// initialize newest threads list
	$('#newest').menu();

	// initialize board list
	$('#boards').menu();

	// initialize message list
	$('#mailboxes').menu();
	$('#messages').menu();

	// initialize user list
	$('#userlist').menu();

	// initialize tabs
	$('#tabs').tabs();
	$('#usercp-tabs').tabs();

	$('#thread-title').html('<i>kein Thread ge&ouml;ffnet</i>');

	// initialize notification area
	$('#notifications').click(function() {
		// 4 = notifications
		// 1 = messages
		var type = $(this).data('type');
		switch(type) {
			case 'notification':
				$('#tabs').tabs('select', '#tab-messages');
				loadMessages(false, 4);
				break;
			case 'messages':
				$('#tabs').tabs('select', '#tab-messages');
				loadMessages(false, 1);
				break;
		}
	});

	// initialize status bar
	$('#serverstatus').click(function() {
		$('#tabs').tabs('select', '#tab-status');
	});

	// hide elements
	$('#passworderror').hide();
	$('#main').hide();
	$('#thread-write').hide();
	$('#thread-write-enable').hide();
	$('#messagereader').hide();

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
