<?xml version="1.0" ?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:lima="http://www.lima-city.de/xml/"
	xmlns="http://www.w3.org/1999/xhtml">

	<xsl:template match="/lima:lima/lima:messages">
		<h2><xsl:value-of select="$text_messages" /></h2>
		<ul class="messages">
			<xsl:for-each select="lima:message">
				<li>
					<xsl:text>&#xBB;</xsl:text>
					<a href="?action=message&amp;id={lima:title/@id}">
						<xsl:value-of select="lima:title" />
					</a>
					<xsl:text>&#xAB;</xsl:text>
					<br />
					<xsl:value-of select="$text_date" />
					<xsl:text>: </xsl:text>
					<xsl:value-of select="lima:date" />
					<br />
					<xsl:value-of select="$text_from" />
					<xsl:text>: </xsl:text>
					<a href="?action=profile&amp;user={lima:from}">
						<xsl:value-of select="lima:from" />
					</a>
				</li>
			</xsl:for-each>
		</ul>
	</xsl:template>

	<xsl:template match="lima:lima/lima:message">
		<p>
			<a href="?action=messages"><xsl:value-of select="$text_messages" /></a>
		</p>
		<h2>
			<xsl:text>PN: </xsl:text>
			<i>
				<xsl:value-of select="lima:title" />
			</i>
		</h2>
		<div>
			<xsl:value-of select="$text_date" />
			<xsl:text>: </xsl:text>
			<xsl:value-of select="lima:date" />
			<br />
			<xsl:value-of select="$text_from" />
			<xsl:text>: </xsl:text>
			<a href="?action=profile&amp;user={lima:from}">
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
