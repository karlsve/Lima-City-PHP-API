<?xml version="1.0" ?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:lima="http://www.lima-city.de/xml/"
	xmlns="http://www.w3.org/1999/xhtml">

	<xsl:template match="lima:forum">
		<h2><xsl:text>Forum</xsl:text></h2>
		<ul class="boards">
			<xsl:apply-templates />
		</ul>
	</xsl:template>

	<xsl:template match="lima:forum/lima:board">
		<li>
			<a href="?sid={$sid}&amp;action=board&amp;name={lima:name/@url}">
				<xsl:value-of select="lima:name" />
			</a>
			<br />

			<xsl:text>Beschreibung: </xsl:text>
			<xsl:value-of select="lima:description" />
			<br />

			<xsl:text>Moderatoren: </xsl:text>
			<xsl:choose>
				<xsl:when test="count(lima:moderatoren/lima:moderator) = 0">
					<i><xsl:text>keine</xsl:text></i><br />
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="count(lima:moderatoren/lima:moderator)" />
					<ul class="moderatoren">
						<xsl:apply-templates />
					</ul>
				</xsl:otherwise>
			</xsl:choose>

			<xsl:text>Threads: </xsl:text>
			<xsl:value-of select="lima:topics" />
			<br />

			<xsl:text>Antworten: </xsl:text>
			<xsl:value-of select="lima:answers" />
			<br />

			<xsl:text>Neuester Thread: </xsl:text>
			<xsl:choose>
				<xsl:when test="lima:newestThread/lima:title != ''">
					<a href="?sid={$sid}&amp;action=thread&amp;name={lima:newestThread/lima:title/@url}">
						<xsl:value-of select="lima:newestThread/lima:title" />
					</a>
					<xsl:text> (</xsl:text>
					<a href="?sid={$sid}&amp;action=profile&amp;user={lima:newestThread/lima:author}">
						<xsl:value-of select="lima:newestThread/lima:author" />
					</a>
					<xsl:text>, </xsl:text>
					<xsl:value-of select="lima:newestThread/lima:date" />
					<xsl:text>)</xsl:text>
				</xsl:when>
				<xsl:otherwise>
					<i><xsl:text>keiner</xsl:text></i>
				</xsl:otherwise>
			</xsl:choose>
		</li>
	</xsl:template>

	<xsl:template match="lima:moderatoren/lima:moderator">
		<li><xsl:value-of select="." /></li>
	</xsl:template>

	<xsl:template match="lima:forum/lima:board/lima:name" />
	<xsl:template match="lima:forum/lima:board/lima:description" />
	<xsl:template match="lima:forum/lima:board/lima:topics" />
	<xsl:template match="lima:forum/lima:board/lima:answers" />
	<xsl:template match="lima:forum/lima:board/lima:newestThread/lima:title" />
	<xsl:template match="lima:forum/lima:board/lima:newestThread/lima:author" />
	<xsl:template match="lima:forum/lima:board/lima:newestThread/lima:date" />
</xsl:stylesheet>
