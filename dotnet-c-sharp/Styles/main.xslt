<?xml version="1.0" encoding="utf-8"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="html"/>
  <xsl:template match="/">
   
        <xsl:apply-templates />
     
  </xsl:template>
  
  <xsl:template match="response">
    <h2>User Contacts</h2>
    <hr color="black"/>

    <table border="0" cellspacing="0" cellpadding="5">
      <thead>
        <tr>
          <td class="contactheader">Name</td>
          <td class="contactheader">Picture</td>
         
        </tr>
      </thead>
      <xsl:for-each select="entry">
        <xsl:sort select="displayName" order="ascending"/>
        <tbody>
          <xsl:if test="position()  mod 2 = 1">
            <tr class="odd">
              <td>
                <xsl:value-of select="displayName" />
              </td>
              <td>
                <img>
                  <xsl:attribute name="src">
                    <xsl:value-of select="photos/photo/value" />
                  </xsl:attribute>
                </img>
              </td>
              
            </tr>
          </xsl:if>

          <xsl:if test="position()  mod 2 = 0">
            <tr class="even">
              <td>
                <xsl:value-of select="displayName" />
              </td>
              <td>
                <img>
                  <xsl:attribute name="src">
                    <xsl:value-of select="photos/photo/value" />
                  </xsl:attribute>
                </img>
              </td>
              
            </tr>
          </xsl:if>
          
        </tbody>
    </xsl:for-each>
    </table>
  </xsl:template>
</xsl:stylesheet>