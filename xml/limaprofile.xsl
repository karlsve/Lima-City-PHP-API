<?xml version="1.0" ?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:lima="http://www.lima-city.de/xml/"
	xmlns="http://www.w3.org/1999/xhtml">

	<xsl:template match="/lima:lima/lima:user">
		<h2><xsl:text>Profile - </xsl:text><i><xsl:value-of select="lima:name" /></i></h2>
		<xsl:apply-templates />
	</xsl:template>

	<xsl:template match="lima:user/lima:profile">
		<dl class="profile">
			<xsl:apply-templates />
		</dl>
	</xsl:template>

	<xsl:template match="lima:user/lima:name" />

	<xsl:template match="lima:user/lima:profile/lima:rang">
		<dt><xsl:text>Rang:</xsl:text></dt>
		<dd><xsl:value-of select="." /></dd>
	</xsl:template>

	<xsl:template match="lima:user/lima:profile/lima:gulden">
		<dt><xsl:text>Gulden:</xsl:text></dt>
		<dd><xsl:value-of select="." /></dd>
	</xsl:template>

	<xsl:template match="lima:user/lima:profile/lima:angemeldet-seit">
		<dt><xsl:text>Angemeldet seit:</xsl:text></dt>
		<dd><xsl:value-of select="." /></dd>
	</xsl:template>

	<xsl:template match="lima:user/lima:profile/lima:letzter-login">
		<dt><xsl:text>Letzter Login:</xsl:text></dt>
		<dd><xsl:value-of select="." /></dd>
	</xsl:template>

	<xsl:template match="lima:user/lima:profile/lima:profilaufrufe">
		<dt><xsl:text>Profilaufrufe:</xsl:text></dt>
		<dd><xsl:value-of select="." /></dd>
	</xsl:template>

	<xsl:template match="lima:user/lima:profile/lima:themen">
		<dt><xsl:text>Themen:</xsl:text></dt>
		<dd><xsl:value-of select="." /></dd>
	</xsl:template>

	<xsl:template match="lima:user/lima:profile/lima:beitraege">
		<dt><xsl:text>Beitr&#xE4;ge:</xsl:text></dt>
		<dd><xsl:value-of select="." /></dd>
	</xsl:template>

	<xsl:template match="lima:user/lima:profile/lima:homepage">
		<dt><xsl:text>Homepage:</xsl:text></dt>
		<dd><a href="{.}"><xsl:value-of select="." /></a></dd>
	</xsl:template>

	<xsl:template match="lima:user/lima:profile/lima:geschlecht">
		<dt><xsl:text>Geschlecht:</xsl:text></dt>
		<dd><xsl:value-of select="." /></dd>
	</xsl:template>

	<xsl:template match="lima:user/lima:profile/lima:realer-name">
		<dt><xsl:text>Realer Name:</xsl:text></dt>
		<dd><xsl:value-of select="." /></dd>
	</xsl:template>

	<xsl:template match="lima:user/lima:profile/lima:raucher">
		<dt><xsl:text>Raucher:</xsl:text></dt>
		<dd><xsl:value-of select="." /></dd>
	</xsl:template>

	<xsl:template match="lima:user/lima:profile/lima:hobbys">
		<dt><xsl:text>Hobbys:</xsl:text></dt>
		<dd><xsl:value-of select="." /></dd>
	</xsl:template>

	<xsl:template match="lima:user/lima:profile/lima:motto">
		<dt><xsl:text>Motto:</xsl:text></dt>
		<dd><xsl:value-of select="." /></dd>
	</xsl:template>

	<xsl:template match="lima:user/lima:profile/lima:ueber-mich">
		<dt><xsl:text>&#xDC;ber mich:</xsl:text></dt>
		<dd><xsl:value-of select="." /></dd>
	</xsl:template>

	<xsl:template match="lima:friends">
		<h3><xsl:text>Freunde:</xsl:text></h3>
		<ul class="friends">
			<xsl:apply-templates />
		</ul>
	</xsl:template>

	<xsl:template match="lima:friend">
		<li><a href="?sid={$sid}&amp;action=profile&amp;user={.}"><xsl:value-of select="." /></a></li>
	</xsl:template>

	<xsl:template match="lima:groups">
		<h3><xsl:text>Gruppen:</xsl:text></h3>
		<ul class="groups">
			<xsl:apply-templates />
		</ul>
	</xsl:template>

	<xsl:template match="lima:group">
		<li>
			<!--<a href="?sid={$sid}&amp;action=group&amp;name={.}">-->
				<xsl:value-of select="." />
			<!--</a>-->
		</li>
	</xsl:template>

	<xsl:template match="lima:guestbook">
		<h3><xsl:text>G&#xE4;stebuch:</xsl:text></h3>
		<ul class="posts">
			<xsl:apply-templates />
		</ul>
	</xsl:template>

	<xsl:template match="lima:profiles">
		<h2><xsl:text>Profiles</xsl:text></h2>
		<ul class="profiles">
			<xsl:apply-templates />
		</ul>
	</xsl:template>

	<xsl:template match="lima:profiles/lima:profile">
		<li>
			<a href="?sid={$sid}&amp;action=profile&amp;user={lima:name}">
				<xsl:value-of select="lima:name" />
			</a>
			<xsl:text> (</xsl:text>
			<xsl:value-of select="lima:gulden" />
			<xsl:text> Gulden, Rang </xsl:text>
			<xsl:value-of select="lima:rang" />
			<xsl:text>)</xsl:text>
		</li>
	</xsl:template>

	<xsl:template match="lima:guestbook/lima:entry/lima:author/lima:name" />
	<xsl:template match="lima:guestbook/lima:entry/lima:author/lima:gulden" />
	<xsl:template match="lima:guestbook/lima:entry/lima:date" />

	<xsl:template match="lima:guestbook/lima:entry">
		<li>
			<div class="author">
				<xsl:call-template name="username">
					<xsl:with-param name="name" select="lima:author/lima:name" />
					<xsl:with-param name="deleted" select="lima:author/@deleted" />
				</xsl:call-template>
				<xsl:text> (</xsl:text>
				<xsl:value-of select="lima:date" />
				<xsl:text>)</xsl:text>
			</div>
			<div class="content">
				<xsl:apply-templates />
			</div>
		</li>
	</xsl:template>
</xsl:stylesheet>
