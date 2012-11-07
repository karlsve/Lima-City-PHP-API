<?xml version="1.0" ?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:lima="http://www.lima-city.de/xml/"
	xmlns="http://www.w3.org/1999/xhtml">

	<xsl:template match="lima:actions">
		<h2><xsl:value-of select="$text_actions" /></h2>
		<ul class="actions">
			<li><a href="?action=homepage"><img src="{$icon_house}" /><xsl:text> </xsl:text><xsl:value-of select="$text_homepage" /></a></li>
			<li><a href="?action=forumlist"><img src="{$icon_layoutcontent}" /><xsl:text> </xsl:text><xsl:value-of select="$text_boards" /></a></li>
			<li><a href="?action=messages"><img src="{$icon_email}" /><xsl:text> </xsl:text><xsl:value-of select="$text_messages" /></a></li>
			<li><a href="?action=serverstatus"><img src="{$icon_serverchart}" /><xsl:text> </xsl:text><xsl:value-of select="$text_serverstatus" /></a></li>
			<li><a href="?action=myprofile"><img src="{$icon_book}" /><xsl:text> </xsl:text><xsl:value-of select="$text_myprofile" /></a></li>
			<li><a href="?action=profiles"><img src="{$icon_online}" /><xsl:text> </xsl:text><xsl:value-of select="$text_usersonline" /></a></li>
		</ul>
	</xsl:template>
</xsl:stylesheet>
