<?xml version="1.0" ?>
<!-- lima-city Stylesheet -->

<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:lima="http://www.lima-city.de/xml/"
	xmlns="http://www.w3.org/1999/xhtml">

	<xsl:template name="username">
		<xsl:param name="name" />
		<xsl:param name="deleted" />
		<xsl:choose>
			<xsl:when test="$deleted = 'true'">
				<xsl:value-of select="$name" />
			</xsl:when>
			<xsl:otherwise>
				<a href="?action=profile&amp;user={$name}">
					<xsl:value-of select="$name" />
				</a>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:include href="limatranslation.xsl" />
	<xsl:include href="limaresources.xsl" />
	<xsl:include href="limalogin.xsl" />
	<xsl:include href="limaactions.xsl" />
	<xsl:include href="limastatus.xsl" />
	<xsl:include href="limaforum.xsl" />
	<xsl:include href="limaboard.xsl" />
	<xsl:include href="limathread.xsl" />
	<xsl:include href="limapostform.xsl" />
	<xsl:include href="limaprofile.xsl" />
	<xsl:include href="limamessages.xsl" />
	<xsl:include href="limahomepage.xsl" />

	<xsl:variable name="sid" select="/lima:lima/lima:session" />

	<xsl:template match="/">
		<html>
			<head>
				<title><xsl:value-of select="$text_title" /></title>
				<link rel="stylesheet" type="text/css" href="/xml/lima.css" />
				<meta http-equiv="content-type" content="text/html; charset=utf-8" />
				<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=0.5, maximum-scale=1" />
				<xsl:if test="/lima:lima/lima:session and /lima:lima/lima:errorcode">
					<meta http-equiv="refresh" content="0; url=." />
				</xsl:if>
			</head>
			<body>
				<h1><xsl:text>Lima-City</xsl:text></h1>
				<xsl:if test="/lima:lima/lima:loggedin = 'yes'">
					<p>
						<xsl:value-of select="$text_username" /><xsl:text>: </xsl:text><xsl:value-of select="/lima:lima/lima:username" />
						<xsl:text>, </xsl:text><xsl:value-of select="/lima:lima/lima:role" /><br />
						<a href="."><xsl:value-of select="$text_actions" /></a><xsl:text>, </xsl:text>
						<a href="?action=homepage"><xsl:value-of select="$text_home" /></a><xsl:text>, </xsl:text>
						<a href="?action=logout"><xsl:value-of select="$text_logout" /></a>
					</p>
				</xsl:if>
				<hr />
				<xsl:apply-templates />
				<xsl:choose>
					<xsl:when test="/lima:lima/lima:loggedin = 'no'">
						<xsl:call-template name="loginform" />
					</xsl:when>
					<xsl:otherwise>
						<hr />
						<p>
							<xsl:value-of select="$text_username" /><xsl:text>: </xsl:text><xsl:value-of select="/lima:lima/lima:username" />
							<xsl:text>, </xsl:text><xsl:value-of select="/lima:lima/lima:role" /><br />
							<a href="."><xsl:value-of select="$text_actions" /></a><xsl:text>, </xsl:text>
							<a href="?action=homepage"><xsl:value-of select="$text_home" /></a><xsl:text>, </xsl:text>
							<a href="?action=logout"><xsl:value-of select="$text_logout" /></a>
						</p>
					</xsl:otherwise>
				</xsl:choose>
			</body>
		</html>
	</xsl:template>

	<xsl:template match="lima:lima">
		<xsl:apply-templates />
	</xsl:template>

	<xsl:template match="/lima:lima/lima:errorcode">
		<p><xsl:text>Errorcode: </xsl:text>
		<xsl:variable name="status" select="." />
		<xsl:choose>
			<xsl:when test="$status = 'ok'">
				<xsl:text>ok</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<span class="error"><xsl:text>error</xsl:text></span>
				<xsl:text> (</xsl:text><xsl:value-of select="." /><xsl:text>)</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
		</p>
	</xsl:template>

	<xsl:template match="/lima:lima/lima:session" />
	<xsl:template match="/lima:lima/lima:loggedin" />
	<xsl:template match="/lima:lima/lima:username" />
	<xsl:template match="/lima:lima/lima:usecookie" />
	<xsl:template match="/lima:lima/lima:role" />
</xsl:stylesheet>
