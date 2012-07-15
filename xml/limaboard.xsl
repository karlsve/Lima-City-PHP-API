<?xml version="1.0" ?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns:lima="http://www.lima-city.de/xml/"
	xmlns="http://www.w3.org/1999/xhtml">

	<xsl:template match="lima:board[lima:thread]">
		<h2>
			<xsl:text>Board: </xsl:text>
			<i><xsl:value-of select="@name" /></i>
		</h2>
		<ul class="threads">
			<xsl:apply-templates />
		</ul>
	</xsl:template>

	<xsl:template match="lima:board/lima:thread">
		<li>
			<a href="?sid={$sid}&amp;action=thread&amp;name={lima:url}">
				<xsl:value-of select="lima:name" />
			</a>
			<xsl:text> (</xsl:text>
			<xsl:call-template name="username">
				<xsl:with-param name="name" select="lima:author" />
				<xsl:with-param name="deleted" select="lima:author/@deleted" />
			</xsl:call-template>
			<xsl:text>, </xsl:text>
			<xsl:value-of select="lima:date" />
			<xsl:text>)</xsl:text>
			<br />
			<xsl:value-of select="lima:views" /><xsl:text> Ansichten, </xsl:text>
			<xsl:value-of select="lima:replies" /><xsl:text> Antworten</xsl:text>
		</li>
	</xsl:template>
</xsl:stylesheet>
