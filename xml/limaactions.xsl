<?xml version="1.0" ?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:lima="http://www.lima-city.de/xml/"
	xmlns="http://www.w3.org/1999/xhtml">

	<xsl:template match="lima:actions">
		<h2><xsl:value-of select="$text_actions" /></h2>
		<ul class="actions">
			<li><a href="?action=homepage"><xsl:value-of select="$text_homepage" /></a></li>
			<li><a href="?action=forumlist"><xsl:value-of select="$text_boards" /></a></li>
			<li><a href="?action=messages"><xsl:value-of select="$text_messages" /></a></li>
			<li><a href="?action=serverstatus"><xsl:value-of select="$text_serverstatus" /></a></li>
			<li><a href="?action=myprofile"><xsl:value-of select="$text_myprofile" /></a></li>
			<li><a href="?action=profiles"><xsl:value-of select="$text_usersonline" /></a></li>
		</ul>
	</xsl:template>
</xsl:stylesheet>
