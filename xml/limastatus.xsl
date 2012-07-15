<?xml version="1.0" ?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns:lima="http://www.lima-city.de/xml/"
	xmlns="http://www.w3.org/1999/xhtml">

	<xsl:template match="lima:serverstatus">
		<h2><xsl:text>Server status</xsl:text></h2>
		<ul>
			<xsl:apply-templates />
		</ul>
	</xsl:template>

	<xsl:template match="lima:serverstatus/lima:info">
		<li>
			<xsl:choose>
				<xsl:when test="@time = ''">
					<span class="servererror"><xsl:value-of select="@name" />: X</span>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="@name" />: <xsl:value-of select="@time" />
				</xsl:otherwise>
			</xsl:choose>
		</li>
	</xsl:template>
</xsl:stylesheet>
