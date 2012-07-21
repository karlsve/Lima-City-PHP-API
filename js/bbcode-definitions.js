var limarules = {
	'<root>' : {
		'childs' : 'i,b,u,del,url,code,quote,math,youtube,<text>'
	},
	'i' : {
		'type' : 'noarg',
		'open_tag' : '<i>',
		'close_tag' : '</i>',
		'childs' : 'b,u,del,url,<text>',
		'trim' : true,
		'leaveopen' : true
	},
	'b' : {
		'type' : 'noarg',
		'open_tag' : '<b>',
		'close_tag' : '</b>',
		'childs' : 'i,u,del,url,<text>',
		'trim' : true,
		'leaveopen' : true
	},
	'u' : {
		'type' : 'noarg',
		'open_tag' : '<u>',
		'close_tag' : '</u>',
		'childs' : 'i,b,del,url,<text>',
		'trim' : true,
		'leaveopen' : true
	},
	'del' : {
		'type' : 'noarg',
		'open_tag' : '<del>',
		'close_tag' : '</del>',
		'childs' : 'i,b,u,url,<text>',
		'trim' : true
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
	':biggrin:'	: 'https://www.lima-city.de/images/smilies/biggrin.gif',
	':wink:'	: 'https://www.lima-city.de/images/smilies/wink.gif',
	':cool:'	: 'https://www.lima-city.de/images/smilies/cool.gif',
	':confused:'	: 'https://www.lima-city.de/images/smilies/confused.gif',
	':slant:'	: 'https://www.lima-city.de/images/smilies/slant.gif',
	':angel:'	: 'https://www.lima-city.de/images/smilies/angel.gif',
	':blah:'	: 'https://www.lima-city.de/images/smilies/blah.gif',
	':thumb:'	: 'https://www.lima-city.de/images/smilies/thumb.gif',
	';-)'		: 'https://www.lima-city.de/images/smilies/wink.gif',
	':mad:'		: 'https://www.lima-city.de/images/smilies/mad.gif',
	':eek:'		: 'https://www.lima-city.de/images/smilies/eek.gif',
	':lol:'		: 'https://www.lima-city.de/images/smilies/lol.gif',
	':wave:'	: 'https://www.lima-city.de/images/smilies/wave.gif',
	':nosmile:'	: 'https://www.lima-city.de/images/smilies/nosmile.gif',
	':tongue:'	: 'https://www.lima-city.de/images/smilies/tongue.gif',
	':prost:'	: 'https://www.lima-city.de/images/smilies/prost.gif',
	':spammer:'	: 'https://www.lima-city.de/images/smilies/spammer.gif',
	':megarofl:'	: 'https://www.lima-city.de/images/smilies/megarofl.gif',
	':smile:'	: 'https://www.lima-city.de/images/smilies/smile.gif',
	':smokin:'	: 'https://www.lima-city.de/images/smilies/smokin.gif',
	':king:'	: 'https://www.lima-city.de/images/smilies/king.gif',
	':puke:'	: 'https://www.lima-city.de/images/smilies/puke.gif',
	':wow:'		: 'https://www.lima-city.de/images/smilies/wow.gif',
	':frown:'	: 'https://www.lima-city.de/images/smilies/frown.gif',
	':pissed:'	: 'https://www.lima-city.de/images/smilies/pissed.gif',
	':wazzup:'	: 'https://www.lima-city.de/images/smilies/wazzup.gif',
	':fart:'	: 'https://www.lima-city.de/images/smilies/fart.gif',
	':holy:'	: 'https://www.lima-city.de/images/smilies/holy.gif',
	':frust:'	: 'https://www.lima-city.de/images/smilies/frust.gif',
	':scared:'	: 'https://www.lima-city.de/images/smilies/scared.gif',
	':sex:'		: 'https://www.lima-city.de/images/smilies/sex.gif',
	':stupid:'	: 'https://www.lima-city.de/images/smilies/stupid.gif',
	':thefinger:'	: 'https://www.lima-city.de/images/smilies/thefinger.gif',
	':love:'	: 'https://www.lima-city.de/images/smilies/love.gif',
	':sauer:'	: 'https://www.lima-city.de/images/smilies/sauer.gif',
	':rolleyes:'	: 'https://www.lima-city.de/images/smilies/rolleyes.gif',
	':sound:'	: 'https://www.lima-city.de/images/smilies/sound.gif',
	':kiss:'	: 'https://www.lima-city.de/images/smilies/kiss.gif',
	':shaft:'	: 'https://www.lima-city.de/images/smilies/shaft.gif',
	':shy:'		: 'https://www.lima-city.de/images/smilies/shy.gif',
	':singer:'	: 'https://www.lima-city.de/images/smilies/singer.gif',
	':redface:'	: 'https://www.lima-city.de/images/smilies/redface.gif',
	':-('		: 'https://www.lima-city.de/images/smilies/frown.gif',
	':sleep:'	: 'https://www.lima-city.de/images/smilies/sleep.gif',
	':-P'		: 'https://www.lima-city.de/images/smilies/tongue.gif',
	':-D'		: 'https://www.lima-city.de/images/smilies/biggrin.gif',
	':-|'		: 'https://www.lima-city.de/images/smilies/nosmile.gif',
	':-o'		: 'https://www.lima-city.de/images/smilies/wow.gif',
	'8-D'		: 'https://www.lima-city.de/images/smilies/cool.gif',
	':cookie:'	: 'https://www.lima-city.de/images/smilies/cookie.gif',
	':wall:'	: 'https://www.lima-city.de/images/smilies/wall.gif',
	':disapprove:'	: 'https://www.lima-city.de/images/smilies/disapprove.gif',
	':approve:'	: 'https://www.lima-city.de/images/smilies/approve.gif'
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
