var limarules = {
	'<root>' : {
		'childs' : 'i,b,u,del,url,code,quote,math,youtube,<text>',
		'ignore_case' : true
	},
	'i' : {
		'type' : 'noarg',
		'open_tag' : '<i>',
		'close_tag' : '</i>',
		'childs' : 'i,b,u,del,url,<text>',
		'trim' : true,
		'leaveopen' : true
	},
	'b' : {
		'type' : 'noarg',
		'open_tag' : '<b>',
		'close_tag' : '</b>',
		'childs' : 'i,b,u,del,url,<text>',
		'trim' : true,
		'leaveopen' : true
	},
	'u' : {
		'type' : 'noarg',
		'open_tag' : '<u>',
		'close_tag' : '</u>',
		'childs' : 'i,b,u,del,url,<text>',
		'trim' : true,
		'leaveopen' : true
	},
	'del' : {
		'type' : 'noarg',
		'open_tag' : '<del>',
		'close_tag' : '</del>',
		'childs' : '<text>',
		'trim' : true,
		'noparse' : true
	},
	'url' : {
		'type' : 'optarg',
		'open_tag' : '<a rel="nofollow" href="{PARAM}">',
		'default_arg' : '{CONTENT}',
		'close_tag' : '</a>',
		'childs' : '<text>,b,i,u,del',
		'childs_noarg' : '<text>',
		'trim' : true,
		'smilies' : false,
		'param_handler' : url_param_handler,
		'param_validator' : url_param_validator
	},
	'youtube': {
		'type' : 'noarg',
		'open_tag' : '<iframe src="http://www.youtube.com/embed/',
		'close_tag' : '" width="320" height="240"></iframe>',
		'childs' : '<text>',
		'childs_noarg' : '<text>',
		'trim' : true,
		'smilies' : false,
		'content_handler' : youtube_content_handler,
		'content_validator' : youtube_content_validator
	},
	'code' : {
		'type' : 'optarg',
		'open_tag' : '<pre{PARAM}>',
		'close_tag' : '</pre>',
		'default_arg' : '',
		'childs' : '<text>',
		'smilies' : false,
		'content_handler' : code_content_handler,
		'param_handler' : code_param_handler,
		'noparse' : true
	},
	'quote' : {
		'type' : 'noarg',
		'open_tag' : '<blockquote>',
		'close_tag' : '</blockquote>',
		'childs' : 'i,b,u,del,url,code,math,quote,youtube,<text>',
		'trim' : true,
		'leaveopen' : true
	},
	'math' : {
		'type' : 'noarg',
		'open_tag' : '<img ',
		'close_tag' : ' />',
		'childs' : '<text>',
		'content_handler' : math_content_handler,
	}
};

var limasmilies = {
	':biggrin:'	: '/images/smilies/biggrin.gif',
	':wink:'	: '/images/smilies/wink.gif',
	':cool:'	: '/images/smilies/cool.gif',
	':confused:'	: '/images/smilies/confused.gif',
	':slant:'	: '/images/smilies/slant.gif',
	':angel:'	: '/images/smilies/angel.gif',
	':blah:'	: '/images/smilies/blah.gif',
	':thumb:'	: '/images/smilies/thumb.gif',
	';-)'		: '/images/smilies/wink.gif',
	':mad:'		: '/images/smilies/mad.gif',
	':eek:'		: '/images/smilies/eek.gif',
	':lol:'		: '/images/smilies/lol.gif',
	':wave:'	: '/images/smilies/wave.gif',
	':nosmile:'	: '/images/smilies/nosmile.gif',
	':tongue:'	: '/images/smilies/tongue.gif',
	':prost:'	: '/images/smilies/prost.gif',
	':spammer:'	: '/images/smilies/spammer.gif',
	':megarofl:'	: '/images/smilies/megarofl.gif',
	':smile:'	: '/images/smilies/smile.gif',
	':smokin:'	: '/images/smilies/smokin.gif',
	':king:'	: '/images/smilies/king.gif',
	':puke:'	: '/images/smilies/puke.gif',
	':wow:'		: '/images/smilies/wow.gif',
	':frown:'	: '/images/smilies/frown.gif',
	':pissed:'	: '/images/smilies/pissed.gif',
	':wazzup:'	: '/images/smilies/wazzup.gif',
	':fart:'	: '/images/smilies/fart.gif',
	':holy:'	: '/images/smilies/holy.gif',
	':frust:'	: '/images/smilies/frust.gif',
	':scared:'	: '/images/smilies/scared.gif',
	':sex:'		: '/images/smilies/sex.gif',
	':stupid:'	: '/images/smilies/stupid.gif',
	':thefinger:'	: '/images/smilies/thefinger.gif',
	':love:'	: '/images/smilies/love.gif',
	':sauer:'	: '/images/smilies/sauer.gif',
	':rolleyes:'	: '/images/smilies/rolleyes.gif',
	':sound:'	: '/images/smilies/sound.gif',
	':kiss:'	: '/images/smilies/kiss.gif',
	':shaft:'	: '/images/smilies/shaft.gif',
	':shy:'		: '/images/smilies/shy.gif',
	':singer:'	: '/images/smilies/singer.gif',
	':redface:'	: '/images/smilies/redface.gif',
	':-('		: '/images/smilies/frown.gif',
	':sleep:'	: '/images/smilies/sleep.gif',
	':-P'		: '/images/smilies/tongue.gif',
	':-D'		: '/images/smilies/biggrin.gif',
	':-|'		: '/images/smilies/nosmile.gif',
	':-o'		: '/images/smilies/wow.gif',
	'8-D'		: '/images/smilies/cool.gif',
	':cookie:'	: '/images/smilies/cookie.gif',
	':wall:'	: '/images/smilies/wall.gif',
	':disapprove:'	: '/images/smilies/disapprove.gif',
	':approve:'	: '/images/smilies/approve.gif'
};

function code_content_handler(content, param) {
	if(code_isValidLanguage(param))
		return htmlunformat(content).trim().replace(/&/g, '&amp;').replace(/</g, '&lt;');
	return '<code>' + htmltrim(content) + '</code>';
}

function code_param_handler(param) {
	if(param == 'inline')
		return ' style="display: inline;"';
	if(code_isValidLanguage(param))
		return ' class="brush: ' + param + '"';
	return '';
}

function code_isValidLanguage(name) {
	switch(name) {
		case 'applescript':
		case 'actionscript3':
		case 'as3':
		case 'bash':
		case 'shell':
		case 'coldfusion':
		case 'cf':
		case 'cpp':
		case 'c':
		case 'c#':
		case 'c-sharp':
		case 'csharp':
		case 'css':
		case 'delphi':
		case 'pascal':
		case 'diff':
		case 'patch':
		case 'pas':
		case 'erl':
		case 'erlang':
		case 'groovy':
		case 'java':
		case 'jfx':
		case 'javafx':
		case 'js':
		case 'jscript':
		case 'javascript':
		case 'perl':
		case 'pl':
		case 'php':
		case 'text':
		case 'plain':
		case 'py':
		case 'python':
		case 'ruby':
		case 'rails':
		case 'ror':
		case 'rb':
		case 'sass':
		case 'scss':
		case 'scala':
		case 'sql':
		case 'vb':
		case 'vbnet':
		case 'xml':
		case 'xhtml':
		case 'xslt':
		case 'html':
			return true;
		default:
			return false;
	}
}

function math_content_handler(content) {
	var formula = htmlunformat(content).trim();
	return 'src="http://www.lima-city.de/math/?' + encodeURIComponent(formula) + '" alt="' + htmlformat(formula) + '" class="latex"';
}

function url_param_handler(url) {
	var check = /^(http|https|ftp)/;
	if(!check.test(url))
		url = 'http://' + url;
	return htmlformat(url.replace(/"/g, '%22'));
}

function url_param_validator(url) {
	var check = /^(http|https|ftp)/;
	if(!check.test(url))
		url = 'http://' + url;
	var pattern = /^((http)|(https)|(ftp)):\/\/([\w\-]+(:[\w\-]+)?@)?((([\w\-\xE4\xF6\xFC\xC4\xD6\xDC\xDF]+\.)+([a-z]{2,4}))|(\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}))(:\d{1,5})?(\/[\.\w\-%,#\?\+:~;=@\(\)\|"\'!\* &\$\/\xE4\xF6\xFC\xC4\xD6\xDC\xDF]*)*(\?([\.\w\-%,#\?\+:~;=@\(\)\|"\'!\* &\$\/\xE4\xF6\xFC\xC4\xD6\xDC\xDF]+)*)?(#[\w\-%]*)?$/i;
	return pattern.test(url);
}

function youtube_content_validator(content) {
	var url = htmlunformat(content);
	var regex1 = /^(https?:\/\/)?(\w+\.)?youtube\.com\/watch\?v=([\w\-]+)/i;
	var regex2 = /^(https?:\/\/)?(\w+\.)?youtu\.be\/([\w\-]+)/i;
	if(!regex1.test(url))
		return regex2.test(url);
	return true;
}

function youtube_content_handler(content) {
	var url = htmlunformat(content);
	var regex1 = /^(https?:\/\/)?(\w+\.)?youtube\.com\/watch\?v=([\w\-]+)/i;
	var regex2 = /^(https?:\/\/)?(\w+\.)?youtu\.be\/([\w\-]+)/i;
	if(!regex1.test(url))
		regex2.exec(url);
	else
		regex1.exec(url);
	return RegExp.$3;
}
