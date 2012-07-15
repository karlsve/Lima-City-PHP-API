<?xml version="1.0" ?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns:lima="http://www.lima-city.de/xml/"
	xmlns="http://www.w3.org/1999/xhtml">

	<xsl:template match="/lima:lima/lima:messages">
		<h2><xsl:text>Nachrichten</xsl:text></h2>
		<ul class="messages">
			<xsl:apply-templates />
		</ul>
	</xsl:template>

	<xsl:template match="lima:messages/lima:message">
		<li>
			<xsl:text>&#xBB;</xsl:text>
			<a href="?sid={$sid}&amp;action=message&amp;id={lima:title/@id}">
				<xsl:value-of select="lima:title" />
			</a>
			<xsl:text>&#xAB;</xsl:text>
			<br />
			<xsl:text>Datum: </xsl:text>
			<xsl:value-of select="lima:date" />
			<br />
			<xsl:text>Von: </xsl:text>
			<a href="?sid={$sid}&amp;action=profile&amp;user={lima:from}">
				<xsl:value-of select="lima:from" />
			</a>
		</li>
	</xsl:template>

	<xsl:template match="lima:lima/lima:message">
		<h2>
			<xsl:text>PN: </xsl:text>
			<i>
				<xsl:value-of select="lima:title" />
			</i>
		</h2>
		<div>
			<xsl:text>Datum: </xsl:text>
			<xsl:value-of select="lima:date" />
			<br />
			<xsl:text>Von: </xsl:text>
			<a href="?sid={$sid}&amp;action=profile&amp;user={lima:from}">
				<xsl:value-of select="lima:from" />
			</a>
		</div>
		<hr />
		<div>
			<xsl:apply-templates />
		</div>
	</xsl:template>

	<xsl:template match="lima:message/lima:title" />
	<xsl:template match="lima:message/lima:date" />
	<xsl:template match="lima:message/lima:from" />
	<xsl:template match="lima:message/lima:to" />
</xsl:stylesheet>
