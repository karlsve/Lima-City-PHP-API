<?xml version="1.0" ?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:lima="http://www.lima-city.de/xml/"
	xmlns="http://www.w3.org/1999/xhtml">

	<xsl:template name="loginform">
		<p><xsl:text>Please log in</xsl:text></p>
		<form action="index.php" method="post">
			<input type="text" name="user" placeholder="Username" /><br />
			<input type="password" name="pass" placeholder="Password" /><br />
			<input type="submit" name="action" value="login" />
		</form>
	</xsl:template>
</xsl:stylesheet>
