<?xml version="1.0" ?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns:lima="http://www.lima-city.de/xml/"
	xmlns="http://www.w3.org/1999/xhtml">

	<xsl:template match="/lima:lima/lima:thread">
		<h2>
			<xsl:text>Thread: </xsl:text>
			<i><xsl:value-of select="lima:name" /></i>
		</h2>
		<ul class="posts">
			<xsl:for-each select="lima:post">
				<li>
					<div class="author">
						<xsl:call-template name="username">
							<xsl:with-param name="name" select="lima:user" />
							<xsl:with-param name="deleted" select="lima:user/@deleted" />
						</xsl:call-template>
						<xsl:text> (</xsl:text>
						<xsl:value-of select="lima:date" />
						<xsl:text>)</xsl:text>
					</div>
					<div class="content">
						<xsl:apply-templates />
					</div>
				</li>
			</xsl:for-each>
		</ul>
		<xsl:call-template name="postform">
			<xsl:with-param name="name" select="lima:url" />
		</xsl:call-template>
	</xsl:template>

	<xsl:template match="lima:thread/lima:name" />
	<xsl:template match="lima:thread/lima:url" />
	<xsl:template match="lima:thread/lima:date" />
	<xsl:template match="lima:thread/lima:post" />

	<xsl:template match="lima:post/lima:type" />
	<xsl:template match="lima:post/lima:date" />
	<xsl:template match="lima:post/lima:id" />
	<xsl:template match="lima:post/lima:user" />

	<xsl:template match="lima:text">
		<xsl:value-of select="." />
	</xsl:template>

	<xsl:template match="lima:br">
		<br />
	</xsl:template>

	<xsl:template match="lima:code">
		<xsl:choose>
			<xsl:when test="@display = 'inline'">
				<pre style="display: inline;"><code>
					<xsl:value-of select="lima:text" />
				</code></pre>
			</xsl:when>
			<xsl:otherwise>
				<pre><code>
					<xsl:value-of select="lima:text" />
				</code></pre>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="lima:blockquote">
		<blockquote>
			<xsl:apply-templates />
		</blockquote>
	</xsl:template>

	<xsl:template match="lima:em">
		<em>
			<xsl:apply-templates />
		</em>
	</xsl:template>

	<xsl:template match="lima:strong">
		<strong>
			<xsl:apply-templates />
		</strong>
	</xsl:template>

	<xsl:template match="lima:del">
		<del>
			<xsl:apply-templates />
		</del>
	</xsl:template>

	<xsl:template match="lima:small">
		<small>
			<xsl:apply-templates />
		</small>
	</xsl:template>

	<xsl:template match="lima:img">
		<img src="{@src}" alt="{@alt}" />
	</xsl:template>

	<xsl:template match="lima:link">
		<a href="{@url}">
			<xsl:apply-templates />
		</a>
	</xsl:template>

	<xsl:template match="lima:goto">
		<a href="?sid={$sid}&amp;action=goto&amp;type={@type}&amp;id={@id}">
			<xsl:apply-templates />
		</a>
	</xsl:template>
</xsl:stylesheet>
