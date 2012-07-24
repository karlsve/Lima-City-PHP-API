<?xml version="1.0" ?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:lima="http://www.lima-city.de/xml/"
	xmlns="http://www.w3.org/1999/xhtml">

	<xsl:template match="lima:actions">
		<h2><xsl:text>Actions</xsl:text></h2>
		<ul>
			<xsl:apply-templates />
		</ul>
	</xsl:template>

	<xsl:template match="lima:action">
		<li><a href="?sid={$sid}&amp;action={.}"><xsl:value-of select="." /></a></li>
	</xsl:template>
</xsl:stylesheet>
