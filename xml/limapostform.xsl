<?xml version="1.0" ?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:lima="http://www.lima-city.de/xml/"
	xmlns="http://www.w3.org/1999/xhtml">

	<xsl:template name="postform">
		<xsl:param name="name" />
		<script type="text/javascript" src="/js/bbcode.js"></script>
		<script type="text/javascript" src="/js/bbcode-definitions.js"></script>
		<script type="text/javascript"><![CDATA[
			// parser rules and smilies are defined in "bbcode-definitions.js"
			var formatter = new BBFormatter(limarules);
			formatter.setSmilies(limasmilies);
			function doformat() {
				var src = document.getElementById('textfield');
				var dst = document.getElementById('formatted-text');
				var chr = document.getElementById('visible-chars');
				var tree = formatter.buildTree(src.value);
				var htmltext = formatter.writeTree(tree);
				dst.innerHTML = htmltext;
				chr.innerHTML = unescape('%3Ci%3Edisplayed characters: %3Cb%3E' + html2plain(htmltext).length + '%3C/b%3E%3C/i%3E'); // character count
			}
		// ]]></script>
		<h3><xsl:text>Neuer Beitrag</xsl:text></h3>
		<h3><xsl:text>Vorschau</xsl:text></h3>
		<ul class="posts">
			<li>
				<div class="author">
					<xsl:text>Vorschau</xsl:text>
				</div>
				<div class="content">
					<div id="formatted-text"></div>
					<div id="visible-chars"></div>
				</div>
			</li>
		</ul>
		<form action="index.php?sid={$sid}" method="post">
			<div>
				<input type="hidden" name="name" value="{$name}" />
				<input type="hidden" name="quotes" value="" />
			</div>
			<div>
				<textarea id="textfield" name="text" onkeyup="doformat();" rows="10"></textarea>
			</div>
			<script type="text/javascript"><![CDATA[
				document.getElementById('textfield').focus();
			]]></script>
			<div>
				<input type="reset" name="action" value="clear" onclick="setTimeout('doformat();', 1);" />
				<input type="submit" name="action" value="post" />
			</div>
		</form>
		<script type="text/javascript"><![CDATA[
			doformat();
		// ]]></script>
	</xsl:template>
</xsl:stylesheet>
