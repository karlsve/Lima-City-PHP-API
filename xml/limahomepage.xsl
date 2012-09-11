<?xml version="1.0" ?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:lima="http://www.lima-city.de/xml/"
	xmlns="http://www.w3.org/1999/xhtml">

	<xsl:template match="lima:homepage">
		<h2><xsl:value-of select="$text_homepage" /></h2>
		<xsl:apply-templates />
	</xsl:template>

	<xsl:template match="lima:homepage/lima:newest">
		<div>
			<h3><xsl:value-of select="$text_newest_posts" /></h3>
			<ul class="threads">
				<xsl:for-each select="lima:thread">
					<li>
						<a href="?action=thread&amp;name={lima:url}">
							<xsl:value-of select="lima:name" />
						</a>
						<xsl:text> (</xsl:text>
						<a href="?action=board&amp;name={lima:forum/@url}"><xsl:value-of select="lima:forum" /></a>
						<xsl:text>, </xsl:text>
						<xsl:call-template name="username">
							<xsl:with-param name="name" select="lima:user" />
							<xsl:with-param name="deleted" select="false" />
						</xsl:call-template>
						<xsl:text>, </xsl:text>
						<xsl:value-of select="lima:date" />
						<xsl:if test="lima:flags/@important = 'true' or lima:flags/@fixed = 'true' or lima:flags/@closed = 'true'">
							<xsl:text>, </xsl:text>
						</xsl:if>
						<xsl:if test="lima:flags/@important = 'true'">
							<img src="{$icon_important}" alt="important" />
						</xsl:if>
						<xsl:if test="lima:flags/@fixed = 'true'">
							<img src="{$icon_fixed}" alt="fixed" />
						</xsl:if>
						<xsl:if test="lima:flags/@closed = 'true'">
							<img src="{$icon_closed}" alt="closed" />
						</xsl:if>
						<xsl:text>)</xsl:text>
					</li>
				</xsl:for-each>
			</ul>
		</div>
	</xsl:template>

	<xsl:template match="lima:homepage/lima:noreply">
		<div>
			<h3><xsl:value-of select="$text_newest_noreply" /></h3>
			<ul class="threads">
				<xsl:for-each select="lima:thread">
					<li>
						<a href="?action=thread&amp;name={lima:url}">
							<xsl:value-of select="lima:name" />
						</a>
						<xsl:text> (</xsl:text>
						<a href="?action=board&amp;name={lima:forum/@url}"><xsl:value-of select="lima:forum" /></a>
						<xsl:text>, </xsl:text>
						<xsl:call-template name="username">
							<xsl:with-param name="name" select="lima:user" />
							<xsl:with-param name="deleted" select="false" />
						</xsl:call-template>
						<xsl:text>, </xsl:text>
						<xsl:value-of select="lima:date" />
						<xsl:if test="lima:flags/@important = 'true' or lima:flags/@fixed = 'true' or lima:flags/@closed = 'true'">
							<xsl:text>, </xsl:text>
						</xsl:if>
						<xsl:if test="lima:flags/@important = 'true'">
							<img src="{$icon_important}" alt="important" />
						</xsl:if>
						<xsl:if test="lima:flags/@fixed = 'true'">
							<img src="{$icon_fixed}" alt="fixed" />
						</xsl:if>
						<xsl:if test="lima:flags/@closed = 'true'">
							<img src="{$icon_closed}" alt="closed" />
						</xsl:if>
						<xsl:text>)</xsl:text>
					</li>
				</xsl:for-each>
			</ul>
		</div>
	</xsl:template>
</xsl:stylesheet>
