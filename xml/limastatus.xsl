<?xml version="1.0" ?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:lima="http://www.lima-city.de/xml/"
	xmlns="http://www.w3.org/1999/xhtml">

	<xsl:template match="lima:serverstatus">
		<h2><xsl:value-of select="$text_serverstatus" /></h2>
		<ul>
			<xsl:for-each select="lima:info">
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
			</xsl:for-each>
		</ul>
	</xsl:template>
</xsl:stylesheet>
