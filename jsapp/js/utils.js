function getDate() {
	var f = function(num) {
		var n = num.toString();
		if(n.length == 1)
			return '0' + n;
		return n;
	};
	var days = [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ];
	var date = new Date();
	return days[date.getDay()] + ', '
		+ f(date.getDate()) + '.' + f(date.getMonth() + 1) + '.' + date.getFullYear()
		+ ' ' + f(date.getHours()) + ':' + f(date.getMinutes());
}
