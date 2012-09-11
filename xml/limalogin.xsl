<?xml version="1.0" ?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:lima="http://www.lima-city.de/xml/"
	xmlns="http://www.w3.org/1999/xhtml">

	<xsl:template name="loginform">
		<p><xsl:value-of select="$text_pleaselogin" /></p>
		<form action="." method="post">
			<input type="text" name="user" placeholder="{$text_username}" /><br />
			<input type="password" name="pass" placeholder="{$text_password}" /><br />
			<input type="hidden" name="usecookie" value="true" />
			<input type="hidden" name="action" value="login" />
			<input type="submit" name="submit" value="{$text_login}" />
		</form>
	</xsl:template>
</xsl:stylesheet>
