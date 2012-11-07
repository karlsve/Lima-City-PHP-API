function XMLRPC(url, root) {
	this.url = 'xmlrpc';
	this.root = 'result';
	if(url != undefined)
		this.url = url;
	if(root != undefined)
		this.root = root;
}

XMLRPC.prototype.setRootTagName = function(name) {
	this.root = name;
};

XMLRPC.prototype.call = function(proc, args, handler) {
	var rootname = this.root;
	var d = {
		'proc' : proc,
		'type' : 'single'
	};
	if((args != undefined) && (args != null))
		d['args'] = JSON.stringify(args);
	$.ajax({
		url : this.url,
		type : 'POST',
		dataType : 'xml',
		data : d
	}).done(function(msg) {
		if((handler != undefined) && (handler != null))
			handler($(msg).find(rootname + ' > result'));
	});
};

XMLRPC.prototype.multicall = function(calls) {
	var d = [];
	var rootname = this.root;
	var refid = 0;
	for(var callname in calls) {
		var call = calls[callname];
		var proc = call['proc'];
		var args = call['args'];
		var ref = 'result-' + refid++;
		calls[callname].ref = ref;
		if(args != undefined)
			d.push({ proc : proc, args : args, ref : ref });
		else
			d.push({ proc : proc, ref : ref });
	}
	$.ajax({
		url : this.url,
		type : 'POST',
		dataType : 'xml',
		data : {
			'type' : 'multi',
			'data' : JSON.stringify(d)
		}
	}).done(function(msg) {
		var rootnode = $(msg).find(rootname);
		for(var callname in calls) {
			var call = calls[callname];
			var proc = call['proc'];
			var handler = call['handler'];
			var ref = call['ref'];
			handler(rootnode.find('> ' + ref));
		}
	});
};
