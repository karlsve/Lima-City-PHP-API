<?xml version="1.0" ?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:lima="http://www.lima-city.de/xml/"
	xmlns="http://www.w3.org/1999/xhtml">

	<xsl:template match="lima:forum">
		<h2><xsl:value-of select="$text_boards" /></h2>
		<ul class="boards">
			<xsl:for-each select="lima:board">
				<li>
					<a href="?action=board&amp;name={lima:name/@url}">
						<xsl:value-of select="lima:name" />
					</a>
					<br />

					<xsl:value-of select="$text_description" />
					<xsl:text>: </xsl:text>
					<xsl:value-of select="lima:description" />
					<br />

					<xsl:value-of select="$text_mods" />
					<xsl:text>: </xsl:text>
					<xsl:choose>
						<xsl:when test="count(lima:moderatoren/lima:moderator) = 0">
							<i><xsl:value-of select="$text_none" /></i><br />
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="count(lima:moderatoren/lima:moderator)" />
							<ul class="moderatoren">
								<xsl:for-each select="lima:moderatoren/lima:moderator">
									<li><xsl:value-of select="." /></li>
								</xsl:for-each>
							</ul>
						</xsl:otherwise>
					</xsl:choose>

					<xsl:value-of select="$text_threads" />
					<xsl:text>: </xsl:text>
					<xsl:value-of select="lima:topics" />
					<br />

					<xsl:value-of select="$text_replies" />
					<xsl:text>: </xsl:text>
					<xsl:value-of select="lima:answers" />
					<br />

					<xsl:value-of select="$text_newestthread" />
					<xsl:text>: </xsl:text>
					<xsl:choose>
						<xsl:when test="lima:newestThread/lima:title != ''">
							<a href="?action=thread&amp;name={lima:newestThread/lima:title/@url}">
								<xsl:value-of select="lima:newestThread/lima:title" />
							</a>
							<xsl:text> (</xsl:text>
							<a href="?action=profile&amp;user={lima:newestThread/lima:author}">
								<xsl:value-of select="lima:newestThread/lima:author" />
							</a>
							<xsl:text>, </xsl:text>
							<xsl:value-of select="lima:newestThread/lima:date" />
							<xsl:text>)</xsl:text>
						</xsl:when>
						<xsl:otherwise>
							<i><xsl:value-of select="$text_none" /></i>
						</xsl:otherwise>
					</xsl:choose>
				</li>
			</xsl:for-each>
		</ul>
	</xsl:template>

</xsl:stylesheet>
